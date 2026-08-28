<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_orders_list(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order1 = Order::factory()->for($user1)->create();
        OrderItem::factory()->for($order1)->create();

        $order2 = Order::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->getJson('/api/v1/orders');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $order1->id);
    }

    public function test_user_cannot_view_order_of_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $orderOfUser2 = Order::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->getJson('/api/v1/orders/'.$orderOfUser2->id);

        $response->assertNotFound();
    }

    public function test_user_can_view_own_order_detail(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'order_code' => 'FO-TEST-001',
            'subtotal' => 100000,
            'total_price' => 100000,
        ]);
        OrderItem::factory()->for($order)->create([
            'food_name' => 'Món Ăn Test',
            'unit_price' => 50000,
            'quantity' => 2,
            'line_total' => 100000,
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/orders/'.$order->id);

        $response->assertOk()
            ->assertJsonPath('data.order_code', 'FO-TEST-001')
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.food_name', 'Món Ăn Test');
    }

    public function test_user_can_cancel_own_pending_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::Pending,
        ]);

        $response = $this->actingAs($user)->patchJson('/api/v1/orders/'.$order->id.'/cancel', [
            'reason' => 'Đổi ý không muốn ăn nữa',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.cancel_reason', 'Đổi ý không muốn ăn nữa');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::Cancelled->value,
            'cancel_reason' => 'Đổi ý không muốn ăn nữa',
        ]);
    }

    public function test_user_cannot_cancel_confirmed_or_delivering_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::Confirmed,
        ]);

        $response = $this->actingAs($user)->patchJson('/api/v1/orders/'.$order->id.'/cancel', [
            'reason' => 'Muốn hủy',
        ]);

        $response->assertStatus(409);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::Confirmed->value,
        ]);
    }

    public function test_user_cannot_cancel_another_users_order(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order2 = Order::factory()->for($user2)->create([
            'status' => OrderStatus::Pending,
        ]);

        $response = $this->actingAs($user1)->patchJson('/api/v1/orders/'.$order2->id.'/cancel');

        $response->assertForbidden();
    }
}
