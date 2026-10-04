<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Food;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_apply_percent_voucher_at_checkout(): void
    {
        $user = User::factory()->create();
        $food = Food::factory()->create([
            'category_id' => Category::factory(),
            'price' => 100000,
        ]);
        Voucher::create([
            'code' => 'GIAM20',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'min_order_value' => 50000,
            'max_discount_amount' => 15000,
            'usage_limit' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Nguyễn Văn A',
            'customer_phone' => '0912345678',
            'delivery_address' => '123 Đường Test',
            'voucher_code' => 'giam20',
            'items' => [['food_id' => $food->id, 'quantity' => 1]],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.discount_amount', 15000)
            ->assertJsonPath('data.total_price', 85000)
            ->assertJsonPath('data.status_history.0.to_status', 'pending');

        $this->assertDatabaseHas('vouchers', ['code' => 'GIAM20', 'used_count' => 1]);
    }

    public function test_invalid_voucher_does_not_create_order(): void
    {
        $user = User::factory()->create();
        $food = Food::factory()->create(['category_id' => Category::factory()]);

        $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Nguyễn Văn A',
            'customer_phone' => '0912345678',
            'delivery_address' => '123 Đường Test',
            'voucher_code' => 'NOT_FOUND',
            'items' => [['food_id' => $food->id, 'quantity' => 1]],
        ])->assertUnprocessable();

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_cancelling_order_releases_voucher_usage(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::Pending,
        ]);
        $voucher = Voucher::create([
            'code' => 'FIXED10',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'used_count' => 1,
            'is_active' => true,
        ]);
        $order->update(['voucher_id' => $voucher->id]);

        $this->actingAs($user)->patchJson('/api/v1/orders/'.$order->id.'/cancel')
            ->assertOk();

        $this->assertDatabaseHas('vouchers', ['id' => $voucher->id, 'used_count' => 0]);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => OrderStatus::Cancelled->value,
        ]);
    }
}
