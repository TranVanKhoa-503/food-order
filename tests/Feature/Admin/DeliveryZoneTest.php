<?php

namespace Tests\Feature\Admin;

use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryZoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_only_sees_active_delivery_zones(): void
    {
        DeliveryZone::factory()->create(['name' => 'Quận 1', 'fee' => 15000, 'is_active' => true]);
        DeliveryZone::factory()->create(['name' => 'Ngoài vùng', 'fee' => 50000, 'is_active' => false]);

        $this->getJson('/api/v1/delivery-zones')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Quận 1')
            ->assertJsonPath('data.0.fee', 15000);
    }

    public function test_admin_can_create_and_update_delivery_zone(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/delivery-zones', [
            'name' => 'Quận 3',
            'fee' => 12000,
            'is_active' => true,
        ]);

        $response->assertCreated()->assertJsonPath('data.name', 'Quận 3');
        $zone = DeliveryZone::query()->firstOrFail();

        $this->actingAs($admin)->putJson('/api/v1/admin/delivery-zones/'.$zone->id, [
            'name' => 'Quận 3 mở rộng',
            'fee' => 18000,
            'is_active' => false,
        ])->assertOk()->assertJsonPath('data.fee', 18000);

        $this->assertDatabaseHas('delivery_zones', [
            'id' => $zone->id,
            'name' => 'Quận 3 mở rộng',
            'fee' => 18000,
            'is_active' => 0,
        ]);
    }
}
