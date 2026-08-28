<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_orders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/v1/admin/orders')
            ->assertForbidden();
    }

    public function test_admin_can_list_all_orders_with_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Order::factory()->for($user)->create([
            'order_code' => 'FO-TARGET-123',
            'customer_name' => 'Nguyễn Tìm Kiếm',
            'status' => OrderStatus::Pending,
        ]);
        Order::factory()->for($user)->create([
            'order_code' => 'FO-OTHER-456',
            'customer_name' => 'Người Khác',
            'status' => OrderStatus::Completed,
        ]);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/orders?search=TARGET');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_code', 'FO-TARGET-123');

        $responseStatus = $this->actingAs($admin)->getJson('/api/v1/admin/orders?status=completed');
        $responseStatus->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_code', 'FO-OTHER-456');
    }

    public function test_admin_can_view_order_details(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();
        OrderItem::factory()->for($order)->create(['food_name' => 'Món Chi Tiết']);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/orders/'.$order->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.food_name', 'Món Chi Tiết');
    }

    public function test_admin_can_transition_order_step_by_step_to_completion(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        // 1. Pending -> Confirmed
        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => 'confirmed',
        ])->assertOk()->assertJsonPath('data.status', 'confirmed');

        // 2. Confirmed -> Preparing
        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => 'preparing',
        ])->assertOk()->assertJsonPath('data.status', 'preparing');

        // 3. Preparing -> Delivering
        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => 'delivering',
        ])->assertOk()->assertJsonPath('data.status', 'delivering');

        // 4. Delivering -> Completed
        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => 'completed',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.payment_status', 'paid');

        $order->refresh();
        $this->assertSame(OrderStatus::Completed, $order->status);
        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
        $this->assertNotNull($order->completed_at);
    }

    public function test_admin_can_cancel_order_with_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Preparing,
        ]);

        $response = $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => 'cancelled',
            'reason' => 'Hết nguyên liệu bất khả kháng',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.cancel_reason', 'Hết nguyên liệu bất khả kháng');

        $order->refresh();
        $this->assertSame(OrderStatus::Cancelled, $order->status);
        $this->assertNotNull($order->cancelled_at);
    }

    public function test_admin_cannot_skip_transitions_or_change_terminal_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Pending,
        ]);

        // Pending -> Completed (nhảy cóc không hợp lệ)
        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => 'completed',
        ])->assertStatus(409);

        // Chuyển sang Cancelled
        $order->update(['status' => OrderStatus::Cancelled]);

        // Cancelled -> Confirmed (đơn đã hủy không được hồi sinh)
        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => 'confirmed',
        ])->assertStatus(409);
    }
}
