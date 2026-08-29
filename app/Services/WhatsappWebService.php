<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsappWebService
{
    public function status(): array
    {
        try {
            return $this->client()->get('/status')->throw()->json();
        } catch (ConnectionException) {
            return ['state' => 'offline', 'connected' => false, 'qr' => null, 'account' => null, 'error' => 'El servicio de WhatsApp no esta iniciado.'];
        }
    }

    public function send(string $phone, string $message): array
    {
        try {
            $response = $this->client()->post('/send', compact('phone', 'message'));
        } catch (ConnectionException) {
            throw new RuntimeException('El servicio de WhatsApp no esta iniciado.');
        }

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?: 'No se pudo enviar el mensaje por WhatsApp.');
        }

        return $response->json();
    }

    public function logout(): void
    {
        try {
            $response = $this->client()->post('/logout');
        } catch (ConnectionException) {
            throw new RuntimeException('El servicio de WhatsApp no esta iniciado.');
        }

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?: 'No se pudo desconectar WhatsApp.');
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.whatsapp_web.url'), '/'))
            ->withToken(config('services.whatsapp_web.token'))
            ->acceptJson()
            ->timeout(15);
    }
}
