<?php

namespace Tests\Feature;

use App\Models\DeliveryLocation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_can_share_and_read_delivery_locations(): void
    {
        $user = User::factory()->create(['name' => 'Repartidor Uno']);
        $role = Role::create(['name' => 'Super admin', 'slug' => Role::SUPER_ADMIN]);
        $user->roles()->attach($role);

        $this->actingAs($user)->postJson(route('admin.deliveries.locations.store'), [
            'latitude' => 19.432608,
            'longitude' => -99.133209,
            'accuracy' => 12.4,
        ])->assertOk()->assertJsonStructure(['recorded_at']);

        $this->assertDatabaseHas('delivery_locations', [
            'user_id' => $user->id,
            'accuracy' => 12,
        ]);

        $this->actingAs($user)->getJson(route('admin.deliveries.locations', ['date' => now()->toDateString()]))
            ->assertOk()
            ->assertJsonPath('drivers.0.name', 'Repartidor Uno')
            ->assertJsonPath('drivers.0.points.0.lat', 19.432608);
    }

    public function test_a_guest_cannot_publish_a_location(): void
    {
        $this->postJson(route('admin.deliveries.locations.store'), [
            'latitude' => 19.432608,
            'longitude' => -99.133209,
        ])->assertUnauthorized();

        $this->assertSame(0, DeliveryLocation::count());
    }
}
