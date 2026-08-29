<?php

namespace App\Services;

use App\Models\CatalogProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleMerchantService
{
    public function syncCatalog(): array
    {
        $this->assertConfigured();
        $result = ['synced' => 0, 'skipped' => 0, 'errors' => []];
        foreach (CatalogProduct::query()->active()->orderBy('id')->get() as $product) {
            if (! $this->isEligible($product)) { $result['skipped']++; continue; }
            try { $this->upsert($product); $result['synced']++; }
            catch (\Throwable $e) { report($e); $result['errors'][] = $product->name.': '.$e->getMessage(); }
        }
        return $result;
    }

    public function configured(): bool
    {
        return filled(config('services.google_merchant.account_id'))
            && filled(config('services.google_merchant.data_source'))
            && is_file($this->credentialsPath());
    }

    private function upsert(CatalogProduct $product): void
    {
        $account = config('services.google_merchant.account_id');
        $source = config('services.google_merchant.data_source');
        $store = rtrim((string) config('services.google_merchant.store_url'), '/');
        Http::acceptJson()->withToken($this->accessToken())->timeout(45)
            ->post("https://merchantapi.googleapis.com/products/v1/accounts/{$account}/productInputs:insert?dataSource=".urlencode($source), [
                'offerId' => 'forjalab-'.$product->id,
                'contentLanguage' => 'es',
                'feedLabel' => 'MX',
                'productAttributes' => [
                    'title' => $product->name,
                    'description' => strip_tags((string) $product->description),
                    'link' => $store.'/catalogo/'.$product->slug,
                    'imageLink' => $this->absoluteUrl($product->cover_photo_path ?: $product->image_path, $store),
                    'availability' => $product->stock > 0 ? 'IN_STOCK' : 'OUT_OF_STOCK',
                    'condition' => 'NEW',
                    'brand' => 'ForjaLab',
                    'identifierExists' => false,
                    'price' => ['amountMicros' => (string) round((float) $product->public_price * 1000000), 'currencyCode' => 'MXN'],
                ],
            ])->throw();
    }

    private function isEligible(CatalogProduct $product): bool
    {
        return (float) $product->public_price > 0 && filled($product->description)
            && filled($product->cover_photo_path ?: $product->image_path);
    }

    private function accessToken(): string
    {
        return Cache::remember('google-merchant.access-token', now()->addMinutes(50), function () {
            $credentials = json_decode((string) file_get_contents($this->credentialsPath()), true, 512, JSON_THROW_ON_ERROR);
            $now = time();
            $header = $this->base64Url((string) json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $this->base64Url((string) json_encode(['iss' => $credentials['client_email'], 'scope' => 'https://www.googleapis.com/auth/content', 'aud' => 'https://oauth2.googleapis.com/token', 'iat' => $now, 'exp' => $now + 3600]));
            if (! openssl_sign($header.'.'.$claims, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) throw new RuntimeException('No se pudo firmar la credencial de Google.');
            $token = Http::asForm()->timeout(30)->post('https://oauth2.googleapis.com/token', ['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $header.'.'.$claims.'.'.$this->base64Url($signature)])->throw()->json('access_token');
            if (! $token) throw new RuntimeException('Google no devolvio un token de acceso.');
            return $token;
        });
    }

    private function assertConfigured(): void
    {
        if (! $this->configured()) throw new RuntimeException('Configura GOOGLE_MERCHANT_ACCOUNT_ID, GOOGLE_MERCHANT_DATA_SOURCE y GOOGLE_MERCHANT_CREDENTIALS.');
    }

    private function credentialsPath(): string
    {
        $path = (string) config('services.google_merchant.credentials');
        return str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) ? $path : base_path($path);
    }

    private function absoluteUrl(string $path, string $store): string { return str_starts_with($path, 'http') ? $path : $store.'/'.ltrim($path, '/'); }
    private function base64Url(string $value): string { return rtrim(strtr(base64_encode($value), '+/', '-_'), '='); }
}
