<?php

namespace App\Mail;

use App\Models\SmartTag;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmergencyScanAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array{latitude: float|int|string, longitude: float|int|string, accuracy?: float|int|string|null, mapsUrl: string, scannedAt: mixed, ip: string|null} $scan
     */
    public function __construct(
        public SmartTag $tag,
        public array $scan,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alerta de escaneo QR: '.$this->tag->display_name.' ('.$this->tag->tag_code.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tag-scan-alert',
            with: [
                'tag' => $this->tag,
                'latitude' => $this->scan['latitude'],
                'longitude' => $this->scan['longitude'],
                'accuracy' => $this->scan['accuracy'] ?? null,
                'mapsUrl' => $this->scan['mapsUrl'],
                'scannedAt' => $this->scan['scannedAt'],
                'ip' => $this->scan['ip'],
            ],
        );
    }
}
