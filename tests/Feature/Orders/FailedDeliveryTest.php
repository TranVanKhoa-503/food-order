<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FailedDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_delivering_order_as_failed_delivery_with_reason(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();

        $order = Order::factory()->for($customer)->create([
            'status' => OrderStatus::Delivering,
        ]);

        $response = $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => OrderStatus::Cancelled->value,
            'reason' => 'Giao hàng không thành công: Khách không nghe máy sau 3 cuộc gọi',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', OrderStatus::Cancelled->value);

        $order->refresh();
        $this->assertSame(OrderStatus::Cancelled, $order->status);
        $this->assertNotNull($order->cancelled_at);
        $this->assertSame('Giao hàng không thành công: Khách không nghe máy sau 3 cuộc gọi', $order->cancel_reason);

        // Verify status history
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'actor_id' => $admin->id,
            'from_status' => OrderStatus::Delivering->value,
            'to_status' => OrderStatus::Cancelled->value,
            'reason' => 'Giao hàng không thành công: Khách không nghe máy sau 3 cuộc gọi',
        ]);

        // Verify customer received notification
        Notification::assertSentTo($customer, OrderStatusUpdatedNotification::class);
    }

    public function test_admin_cannot_cancel_delivering_order_without_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Delivering,
        ]);

        $response = $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => OrderStatus::Cancelled->value,
            'reason' => '',
        ]);

        $response->assertUnprocessable();
        $this->assertSame(OrderStatus::Delivering, $order->fresh()->status);
    }

    public function test_admin_cannot_cancel_completed_order_directly(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Completed,
        ]);

        $response = $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => OrderStatus::Cancelled->value,
            'reason' => 'Không thể hủy đơn đã hoàn tất',
        ]);

        $response->assertStatus(409);
        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);
    }
}
