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

    public function test_biker_insurance_information_is_saved_and_displayed(): void
    {
        $tag = SmartTag::create([
            'type' => SmartTag::TYPE_BIKER,
            'display_name' => 'Rider asegurado',
            'has_vehicle_insurance' => true,
            'vehicle_insurance_policy' => 'POL-12345',
            'vehicle_insurance_expires_at' => '2027-08-07',
            'has_public_health_insurance' => true,
            'public_health_provider' => 'imss',
            'public_health_number' => 'NSS-987654',
        ]);

        $this->assertTrue($tag->has_vehicle_insurance);
        $this->assertSame('IMSS', $tag->public_health_provider_label);

        $this->get(route('tags.public', $tag->token))
            ->assertOk()
            ->assertSee('POL-12345')
            ->assertSee('07/08/2027')
            ->assertSee('IMSS')
            ->assertSee('NSS-987654');
    }
}
