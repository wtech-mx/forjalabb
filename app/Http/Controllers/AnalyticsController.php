<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class AnalyticsController extends Controller
{
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'event_type' => ['required', Rule::in(['page_view', 'section_view', 'product_click', 'whatsapp_click'])],
            'path' => ['required', 'string', 'max:500'],
            'label' => ['nullable', 'string', 'max:160'],
            'session_id' => ['required', 'string', 'max:64'],
            'referrer' => ['nullable', 'string', 'max:500'],
            'utm_source' => ['nullable', 'string', 'max:120'],
            'utm_medium' => ['nullable', 'string', 'max:120'],
            'utm_campaign' => ['nullable', 'string', 'max:160'],
        ]);

        $userAgent = strtolower((string) $request->userAgent());
        $data['device'] = str_contains($userAgent, 'mobile') ? 'mobile' : (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad') ? 'tablet' : 'desktop');
        $data['occurred_at'] = now();

        AnalyticsEvent::create($data);

        return response()->noContent();
    }
}
