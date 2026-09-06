<?php

namespace App\Services;

use App\Models\GoogleDriveToken;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleDriveGalleryService
{
    private const SCOPE = 'https://www.googleapis.com/auth/drive';

    public function configured(): bool
    {
        return filled(config('services.drive_gallery.client_id'))
            && filled(config('services.drive_gallery.client_secret'))
            && filled(config('services.drive_gallery.redirect_uri'));
    }

    public function connected(): bool
    {
        return $this->configured() && GoogleDriveToken::where('provider', 'google_drive')->exists();
    }

    public function authorizationUrl(string $state): string
    {
        $this->ensureConfigured();

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('services.drive_gallery.client_id'),
            'redirect_uri' => config('services.drive_gallery.redirect_uri'),
            'response_type' => 'code',
            'scope' => self::SCOPE,
            'access_type' => 'offline',
            'include_granted_scopes' => 'true',
            'prompt' => 'consent',
            'state' => $state,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public function exchangeCode(User $user, string $code): GoogleDriveToken
    {
        $this->ensureConfigured();
        $payload = Http::asForm()->timeout(30)->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.drive_gallery.client_id'),
            'client_secret' => config('services.drive_gallery.client_secret'),
            'redirect_uri' => config('services.drive_gallery.redirect_uri'),
            'grant_type' => 'authorization_code',
            'code' => $code,
        ])->throw()->json();

        if (blank($payload['access_token'] ?? null) || blank($payload['refresh_token'] ?? null)) {
            throw new RuntimeException('Google no devolvió los tokens necesarios. Intenta conectar nuevamente.');
        }

        return GoogleDriveToken::updateOrCreate(
            ['provider' => 'google_drive'],
            [
                'connected_by_user_id' => $user->id,
                'access_token' => $payload['access_token'],
                'refresh_token' => $payload['refresh_token'],
                'expires_at' => now()->addSeconds(max(60, (int) ($payload['expires_in'] ?? 3600)) - 30),
                'scope' => $payload['scope'] ?? self::SCOPE,
            ]
        );
    }

    public function upload(UploadedFile $file, string $folderId): array
    {
        $boundary = 'forjalab_'.bin2hex(random_bytes(12));
        $metadata = json_encode(['name' => $file->getClientOriginalName(), 'parents' => [$folderId]], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $body = "--{$boundary}\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadata}\r\n";
        $body .= "--{$boundary}\r\nContent-Type: {$file->getMimeType()}\r\n\r\n".$file->get()."\r\n--{$boundary}--";

        return Http::withToken($this->accessToken())
            ->withBody($body, 'multipart/related; boundary='.$boundary)
            ->timeout(120)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,mimeType,size,parents,webViewLink')
            ->throw()
            ->json();
    }

    private function accessToken(): string
    {
        $token = GoogleDriveToken::where('provider', 'google_drive')->first();
        if (! $token) {
            throw new RuntimeException('Primero conecta tu cuenta de Google Drive.');
        }

        if ($token->expires_at?->isFuture()) {
            return $token->access_token;
        }

        $payload = Http::asForm()->timeout(30)->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.drive_gallery.client_id'),
            'client_secret' => config('services.drive_gallery.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->refresh_token,
        ])->throw()->json();

        if (blank($payload['access_token'] ?? null)) {
            throw new RuntimeException('No fue posible renovar el acceso a Google Drive. Conecta la cuenta nuevamente.');
        }

        $token->update([
            'access_token' => $payload['access_token'],
            'expires_at' => now()->addSeconds(max(60, (int) ($payload['expires_in'] ?? 3600)) - 30),
            'scope' => $payload['scope'] ?? $token->scope,
        ]);

        return $token->access_token;
    }

    private function ensureConfigured(): void
    {
        if (! $this->configured()) {
            throw new RuntimeException('Faltan GOOGLE_DRIVE_CLIENT_ID, GOOGLE_DRIVE_CLIENT_SECRET o GOOGLE_DRIVE_REDIRECT_URI.');
        }
    }
}
