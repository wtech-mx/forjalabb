<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaignRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    public function open(string $token): Response
    {
        $recipient = EmailCampaignRecipient::where('tracking_token', $token)->first();
        if ($recipient) {
            $recipient->forceFill([
                'opened_at' => $recipient->opened_at ?: now(),
                'open_count' => $recipient->open_count + 1,
            ])->save();
        }

        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function click(string $token): RedirectResponse
    {
        $recipient = EmailCampaignRecipient::where('tracking_token', $token)->firstOrFail();
        $url = (string) request()->query('url');
        abort_unless(filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true), 400);

        $recipient->forceFill([
            'clicked_at' => $recipient->clicked_at ?: now(),
            'click_count' => $recipient->click_count + 1,
            'last_clicked_url' => $url,
        ])->save();

        return redirect()->away($url);
    }
}
