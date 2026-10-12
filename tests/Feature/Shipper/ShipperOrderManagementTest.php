<?php

namespace Tests\Feature\Shipper;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipperOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_accessing_shipper_portal(): void
    {
        $response = $this->get('/shipper/orders');
        $response->assertRedirect('/login');

        $apiResponse = $this->getJson('/api/v1/shipper/orders');
        $apiResponse->assertUnauthorized();
    }

    public function test_regular_customer_cannot_access_shipper_portal(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get('/shipper/orders')
            ->assertForbidden();

        $this->actingAs($customer)->getJson('/api/v1/shipper/orders')
            ->assertForbidden();
    }

    public function test_shipper_cannot_access_admin_panel(): void
    {
        $shipper = User::factory()->shipper()->create();

        $this->actingAs($shipper)->get('/admin/dashboard')
            ->assertForbidden();

        $this->actingAs($shipper)->get('/admin/orders')
            ->assertForbidden();

        $this->actingAs($shipper)->getJson('/api/v1/admin/foods')
            ->assertForbidden();
    }

    public function test_shipper_can_access_shipper_portal_and_view_delivering_orders(): void
    {
        $shipper = User::factory()->shipper()->create();

        $deliveringOrder = Order::factory()->create([
            'order_code' => 'ORD-DELIV-101',
            'customer_name' => 'Khách Hàng A',
            'customer_phone' => '0987654321',
            'delivery_address' => '123 Đường Lê Lợi, Q.1',
            'status' => OrderStatus::Delivering,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'payment_status' => PaymentStatus::Unpaid,
            'total_price' => 120000,
        ]);
        OrderItem::factory()->for($deliveringOrder)->create([
            'food_name' => 'Bún Bò Huế Đặc Biệt',
            'quantity' => 2,
        ]);

        $completedOrder = Order::factory()->create([
            'order_code' => 'ORD-COMP-202',
            'status' => OrderStatus::Completed,
        ]);

        // Web view
        $response = $this->actingAs($shipper)->get('/shipper/orders?status=delivering');
        $response->assertOk()
            ->assertSee('ORD-DELIV-101')
            ->assertSee('Khách Hàng A')
            ->assertSee('0987654321')
            ->assertSee('123 Đường Lê Lợi')
            ->assertSee('120.000')
            ->assertDontSee('ORD-COMP-202');

        // API
        $apiResponse = $this->actingAs($shipper)->getJson('/api/v1/shipper/orders?status=delivering');
        $apiResponse->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_code', 'ORD-DELIV-101');
    }

    public function test_admin_can_also_access_shipper_portal(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/shipper/orders')
            ->assertOk();
    }

    public function test_shipper_can_confirm_delivery_success(): void
    {
        $shipper = User::factory()->shipper()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Delivering,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $response = $this->actingAs($shipper)->patch("/shipper/orders/{$order->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(OrderStatus::Completed, $order->status);
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);
        $this->assertNotNull($order->completed_at);

        // Verify status history audit trail recorded shipper as actor
        $history = $order->statusHistories()->latest('id')->first();
        $this->assertNotNull($history);
        $this->assertEquals($shipper->id, $history->actor_id);
        $this->assertEquals('delivering', $history->from_status);
        $this->assertEquals('completed', $history->to_status);
    }

    public function test_shipper_can_report_delivery_failure_with_reason(): void
    {
        $shipper = User::factory()->shipper()->create();
        $customer = User::factory()->create();
        $order = Order::factory()->for($customer)->create([
            'status' => OrderStatus::Delivering,
        ]);

        $response = $this->actingAs($shipper)->patch("/shipper/orders/{$order->id}/status", [
            'status' => 'cancelled',
            'reason' => 'Khách hàng không nghe máy (đã gọi 3 lần)',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(OrderStatus::Cancelled, $order->status);
        $this->assertEquals('Khách hàng không nghe máy (đã gọi 3 lần)', $order->cancel_reason);
        $this->assertNotNull($order->cancelled_at);

        // Verify status history
        $history = $order->statusHistories()->latest('id')->first();
        $this->assertNotNull($history);
        $this->assertEquals($shipper->id, $history->actor_id);
        $this->assertEquals('delivering', $history->from_status);
        $this->assertEquals('cancelled', $history->to_status);
    }

    public function test_shipper_must_provide_reason_when_cancelling(): void
    {
        $shipper = User::factory()->shipper()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Delivering,
        ]);

        $response = $this->actingAs($shipper)->patchJson("/api/v1/shipper/orders/{$order->id}/status", [
            'status' => 'cancelled',
            'reason' => '',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['reason']);
    }

    public function test_shipper_cannot_transition_to_arbitrary_statuses(): void
    {
        $shipper = User::factory()->shipper()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Delivering,
        ]);

        $response = $this->actingAs($shipper)->patchJson("/api/v1/shipper/orders/{$order->id}/status", [
            'status' => 'pending',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_shipper_cannot_update_order_that_is_not_delivering(): void
    {
        $shipper = User::factory()->shipper()->create();
        $pendingOrder = Order::factory()->create([
            'status' => OrderStatus::Pending,
        ]);

        $response = $this->actingAs($shipper)->patchJson("/api/v1/shipper/orders/{$pendingOrder->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertUnprocessable();
        $this->assertNull($pendingOrder->fresh()->shipper_id);
        $this->assertNull($pendingOrder->fresh()->assigned_at);
    }

    public function test_repeated_delivery_failure_redirects_without_repeating_side_effects(): void
    {
        $shipper = User::factory()->shipper()->create();
        $customer = User::factory()->create();
        $voucher = Voucher::create([
            'code' => 'DELIVERY-RETRY',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'used_count' => 2,
        ]);
        $order = Order::factory()->for($customer)->create([
            'status' => OrderStatus::Delivering,
            'voucher_id' => $voucher->id,
        ]);
        $url = "/shipper/orders/{$order->id}/status";
        $payload = ['status' => 'cancelled', 'reason' => 'Khách không nghe máy'];

        $this->actingAs($shipper)->from('/shipper/orders')->patch($url, $payload)
            ->assertRedirect('/shipper/orders')
            ->assertSessionHas('success');

        $cancelledAt = $order->fresh()->cancelled_at;
        $payload['reason'] = 'Lý do ở lần gửi trùng';
        $this->patch($url, $payload)
            ->assertRedirect('/shipper/orders')
            ->assertSessionHas('error')
            ->assertSessionMissing('success');

        $order->refresh();
        $this->assertSame(OrderStatus::Cancelled, $order->status);
        $this->assertSame('Khách không nghe máy', $order->cancel_reason);
        $this->assertEquals($cancelledAt, $order->cancelled_at);
        $this->assertSame($shipper->id, $order->shipper_id);
        $this->assertSame(1, $order->statusHistories()->count());
        $this->assertSame(1, $customer->notifications()->count());
        $this->assertSame(1, $voucher->fresh()->used_count);
    }

    public function test_stale_failure_form_cannot_cancel_or_claim_completed_order(): void
    {
        $shipper = User::factory()->shipper()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Completed,
            'payment_status' => PaymentStatus::Paid,
            'shipper_id' => null,
        ]);

        $this->actingAs($shipper)->patch("/shipper/orders/{$order->id}/status", [
            'status' => 'cancelled',
            'reason' => 'Form mở ở tab cũ',
        ])->assertRedirect('/shipper/orders')->assertSessionHas('error');

        $order->refresh();
        $this->assertSame(OrderStatus::Completed, $order->status);
        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
        $this->assertNull($order->shipper_id);
        $this->assertNull($order->assigned_at);
        $this->assertNull($order->cancelled_at);
        $this->assertSame(0, $order->statusHistories()->count());
    }

    public function test_shipper_cannot_view_another_shippers_order_details(): void
    {
        $shipper = User::factory()->shipper()->create();
        $other = User::factory()->shipper()->create();
        $order = Order::factory()->create(['shipper_id' => $other->id, 'status' => OrderStatus::Delivering]);

        $this->actingAs($shipper)->get('/shipper/orders/'.$order->id)->assertNotFound();
        $this->getJson('/api/v1/shipper/orders/'.$order->id)->assertNotFound();
    }

    public function test_shipper_can_view_assigned_orders_and_unassigned_deliveries_only(): void
    {
        $shipper = User::factory()->shipper()->create();
        $assigned = Order::factory()->create(['shipper_id' => $shipper->id, 'status' => OrderStatus::Completed]);
        $available = Order::factory()->create(['shipper_id' => null, 'status' => OrderStatus::Delivering]);
        $pending = Order::factory()->create(['shipper_id' => null, 'status' => OrderStatus::Pending]);
        $completed = Order::factory()->create(['shipper_id' => null, 'status' => OrderStatus::Completed]);

        $this->actingAs($shipper)->getJson('/api/v1/shipper/orders?status=all')
            ->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/shipper/orders/'.$assigned->id)->assertOk();
        $this->get('/shipper/orders/'.$available->id)->assertOk()
            ->assertSee(route('shipper.orders.index'));
        $this->getJson('/api/v1/shipper/orders/'.$pending->id)->assertNotFound();
        $this->getJson('/api/v1/shipper/orders/'.$completed->id)->assertNotFound();
    }
}
