<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_receives_notification_when_admin_updates_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $order = Order::factory()->for($customer)->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => OrderStatus::Confirmed->value,
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $customer->id,
        ]);

        $this->actingAs($customer)->getJson('/api/v1/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('data.0.order_code', $order->order_code)
            ->assertJsonPath('data.0.status', 'confirmed');
    }

    public function test_customer_can_mark_all_order_notifications_as_read(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $order = Order::factory()->for($customer)->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => OrderStatus::Confirmed->value,
        ]);

        $this->actingAs($customer)->patchJson('/api/v1/notifications/read-all')
            ->assertOk();

        $this->actingAs($customer)->getJson('/api/v1/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 0)
            ->assertJsonPath('data.0.read_at', fn ($value) => $value !== null);
    }
}
