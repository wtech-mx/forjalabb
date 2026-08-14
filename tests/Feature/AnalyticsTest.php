<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_analytics_event_is_recorded_without_personal_data(): void
    {
        $this->postJson(route('analytics.events'), [
            'event_type' => 'product_click',
            'path' => '/servicios/dog-tags',
            'label' => 'Dog Tags QR',
            'session_id' => 'anonymous-session-123',
            'utm_source' => 'google',
        ])->assertNoContent();

        $event = AnalyticsEvent::firstOrFail();

        $this->assertSame('product_click', $event->event_type);
        $this->assertSame('Dog Tags QR', $event->label);
        $this->assertSame('google', $event->utm_source);
    }
}
