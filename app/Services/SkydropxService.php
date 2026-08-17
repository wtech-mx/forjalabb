<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SkydropxService
{
    public function quote(array $quotation): array
    {
        $response = Http::acceptJson()->withToken($this->token())->timeout(30)
            ->post($this->url('/api/v2/quotations'), ['quotation' => $quotation])
            ->throw()->json();

        $quotationId = data_get($response, 'data.id', data_get($response, 'id'));
        for ($attempt = 0; $quotationId && $attempt < 8 && ! $this->hasCompletedPrices($response); $attempt++) {
            usleep(750000);
            $response = Http::acceptJson()->withToken($this->token())->timeout(30)
                ->get($this->url('/api/v1/quotations/'.$quotationId))->throw()->json();
        }

        return $response;
    }

    public function createShipment(string $rateId): array
    {
        return Http::acceptJson()->withToken($this->token())->timeout(60)
            ->post($this->url('/api/v2/shipments'), ['shipment' => [
                'rate_id' => $rateId, 'unique_shipment' => true,
                'printing_format' => 'standard', 'include_order_detail' => true,
            ]])->throw()->json();
    }

    public function rates(array $response): array
    {
        $rates = data_get($response, 'data.rates', data_get($response, 'rates', []));
        if (! is_array($rates)) return [];
        return collect($rates)->filter(fn($rate)=>is_array($rate) && filled($rate['id'] ?? null) && ($rate['success'] ?? true) && $this->ratePrice($rate) > 0)->map(fn($rate)=>[
            'id'=>$rate['id'],
            'carrier'=>$rate['provider_display_name'] ?? $rate['provider_name'] ?? $rate['carrier_name'] ?? data_get($rate,'carrier.name') ?? 'Paquetería',
            'service'=>$rate['provider_service_name'] ?? $rate['service_name'] ?? data_get($rate,'service.name') ?? ($rate['service_level_name'] ?? 'Servicio'),
            'price'=>$this->ratePrice($rate),
            'days'=>$rate['days'] ?? $rate['estimated_delivery'] ?? $rate['delivery_days'] ?? null,
        ])->values()->all();
    }

    private function hasCompletedPrices(array $response): bool
    {
        $completed = (bool) data_get($response, 'data.is_completed', data_get($response, 'is_completed', false));
        return $completed && count($this->rawPricedRates($response)) > 0;
    }

    private function rawPricedRates(array $response): array
    {
        $rates = data_get($response, 'data.rates', data_get($response, 'rates', []));
        return collect(is_array($rates) ? $rates : [])->filter(fn($rate) => is_array($rate) && $this->ratePrice($rate) > 0)->all();
    }

    private function ratePrice(array $rate): float
    {
        return (float) ($rate['total'] ?? $rate['total_price'] ?? data_get($rate, 'total_pricing.total') ?? data_get($rate, 'price.total') ?? $rate['amount'] ?? $rate['price'] ?? 0);
    }

    public function postalCode(string $postalCode): array
    {
        try {
            $response = Http::acceptJson()->timeout(8)->get('https://sepomex.icalialabs.com/api/v1/zip_codes', ['zip_code'=>$postalCode])->throw()->json();
            $rows = data_get($response, 'zip_codes', data_get($response, 'data', []));
            $places=collect(is_array($rows)?$rows:[])->map(fn($row)=>['state'=>$row['d_estado']??$row['state']??'','city'=>$row['d_mnpio']??$row['municipality']??$row['city']??'','neighborhood'=>$row['d_asenta']??$row['settlement']??$row['neighborhood']??''])->filter(fn($row)=>filled($row['neighborhood']))->unique('neighborhood')->values()->all();
            if ($places) return $places;
        } catch (\Throwable) {}
        $response=Http::acceptJson()->timeout(8)->get('https://api.zippopotam.us/MX/'.$postalCode)->throw()->json();
        return collect($response['places']??[])->map(function($row){$state=$row['state']??'';if($state==='Distrito Federal')$state='Ciudad de México';return ['state'=>$state,'city'=>$row['place name']??'','neighborhood'=>$row['place name']??''];})->values()->all();
    }

    private function token(): string
    {
        $clientId = config('services.skydropx.client_id');
        $secret = config('services.skydropx.client_secret');
        if (blank($clientId) || blank($secret)) {
            throw new RuntimeException('Faltan SKYDROPX_CLIENT_ID y SKYDROPX_CLIENT_SECRET en el archivo .env.');
        }

        return Cache::remember('skydropx.oauth.token', now()->addMinutes(110), function () use ($clientId, $secret) {
            $token = Http::acceptJson()->timeout(30)->post($this->url('/api/v1/oauth/token'), [
                'client_id' => $clientId, 'client_secret' => $secret, 'grant_type' => 'client_credentials',
            ])->throw()->json('access_token');
            if (blank($token)) throw new RuntimeException('Skydropx no devolvió un token de acceso.');
            return $token;
        });
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.skydropx.base_url'), '/').$path;
    }
}
