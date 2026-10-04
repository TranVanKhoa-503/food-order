<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStoreSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_store_ordering_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->putJson('/api/v1/admin/settings', [
            'store_name' => 'Quán Ăn Demo',
            'is_open' => true,
            'opens_at' => '08:00',
            'closes_at' => '22:00',
            'min_order_value' => 50000,
            'shipping_fee' => 15000,
            'estimated_delivery_minutes' => 45,
        ])->assertOk()->assertJsonPath('data.shipping_fee', '15000');

        $this->assertDatabaseHas('store_settings', [
            'store_name' => 'Quán Ăn Demo',
            'shipping_fee' => 15000,
            'min_order_value' => 50000,
        ]);
    }

    public function test_regular_user_cannot_update_store_ordering_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->putJson('/api/v1/admin/settings', [
            'store_name' => 'Không hợp lệ',
            'is_open' => true,
            'opens_at' => '00:00',
            'closes_at' => '23:59',
            'min_order_value' => 0,
            'shipping_fee' => 0,
            'estimated_delivery_minutes' => 30,
        ])->assertForbidden();
    }
}
