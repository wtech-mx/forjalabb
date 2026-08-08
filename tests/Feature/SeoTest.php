<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_service_has_search_and_social_metadata(): void
    {
        $this->get(route('services.show', 'biker-tag'))
            ->assertOk()
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('<meta property="og:title"', false)
            ->assertSee('<meta name="twitter:card"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('Kit Biker Tag QR de emergencia');
    }

    public function test_sitemap_and_robots_are_available(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('services.show', 'biker-tag'))
            ->assertSee(route('services.show', 'dog-tags'));

        $this->get(route('robots'))
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.route('sitemap'));
    }
}
