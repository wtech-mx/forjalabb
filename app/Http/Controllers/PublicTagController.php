<?php

namespace App\Http\Controllers;

use App\Models\SmartTag;
use App\Services\EmergencyTagMailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTagController extends Controller
{
    public function __construct(private readonly EmergencyTagMailer $mailer) {}

    public function __invoke(string $token): View
    {
        $tag = SmartTag::where('token', $token)->firstOrFail();

        return view($tag->type === SmartTag::TYPE_BIKER ? 'tags.public-biker' : 'tags.public-dog', compact('tag'));
    }

    public function scan(Request $request, string $token): JsonResponse
    {
        $tag = SmartTag::where('token', $token)->firstOrFail();

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
        ]);

        $recipients = collect([
            $tag->owner_email,
            $tag->secondary_contact_email,
            $tag->type === SmartTag::TYPE_DOG ? $tag->vet_email : null,
        ])->filter()->unique()->values();

        if ($recipients->isEmpty()) {
            return response()->json([
                'message' => 'No hay correos de emergencia capturados para este tag.',
            ], 422);
        }

        $scan = [
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => $data['accuracy'] ?? null,
            'mapsUrl' => 'https://www.google.com/maps?q='.$data['latitude'].','.$data['longitude'],
            'scannedAt' => now(),
            'ip' => $request->ip(),
        ];

        $this->mailer->send($tag, $scan, $recipients->all());

        return response()->json([
            'message' => 'Alerta enviada a los correos de emergencia.',
        ]);
    }
}
