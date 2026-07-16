<?php

namespace Tests\Feature;

use App\Mail\EmergencyScanAlert;
use App\Models\SmartTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmartTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_profile_and_admin_qr_are_available(): void
    {
        $user = User::factory()->create();

        $tag = SmartTag::create([
            'type' => SmartTag::TYPE_BIKER,
            'is_active' => true,
            'is_blood_donor' => true,
            'display_name' => 'Demo Rider',
            'owner_name' => 'Contacto Demo',
            'owner_phone' => '+525500000000',
            'owner_email' => 'contacto@example.test',
            'blood_type' => 'O+',
        ]);

        $this->assertSame('LC-BKR-OP-D-000001', $tag->fresh()->tag_code);

        $this->get(route('tags.public', $tag->token))
            ->assertOk()
            ->assertSee('Demo Rider')
            ->assertSee('O+')
            ->assertSee('Donador');

        $this->actingAs($user)
            ->get(route('admin.tags.qr', $tag))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertSee('<svg', false);

        Mail::fake();

        $this->postJson(route('tags.scan', $tag->token), [
            'latitude' => 19.432608,
            'longitude' => -99.133209,
            'accuracy' => 12,
        ])->assertOk()
            ->assertJsonPath('message', 'Alerta enviada a los correos de emergencia.');

        Mail::assertSent(EmergencyScanAlert::class);
    }
}
