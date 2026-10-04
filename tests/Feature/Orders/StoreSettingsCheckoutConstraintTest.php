<?php

namespace Tests\Feature\Orders;

use App\Models\Category;
use App\Models\Food;
use App\Models\StoreSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSettingsCheckoutConstraintTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_checkout_fails_when_store_is_closed(): void
    {
        StoreSetting::create(array_merge(StoreSetting::defaults(), [
            'is_open' => false,
        ]));

        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'price' => 50000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Mua Hàng',
            'customer_phone' => '0912345678',
            'delivery_address' => '123 Đường Test',
            'items' => [['food_id' => $food->id, 'quantity' => 1]],
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Cửa hàng hiện không nhận đơn.');
    }

    public function test_checkout_fails_when_outside_operating_hours(): void
    {
        // Store open from 08:00 to 22:00
        StoreSetting::create(array_merge(StoreSetting::defaults(), [
            'is_open' => true,
            'opens_at' => '08:00',
            'closes_at' => '22:00',
            'min_order_value' => 0,
        ]));

        // Mock current time to 03:00 AM (outside hours)
        Carbon::setTestNow(Carbon::parse('2026-10-05 03:00:00'));

        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'price' => 50000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Đêm',
            'customer_phone' => '0912345678',
            'delivery_address' => '123 Đường Test',
            'items' => [['food_id' => $food->id, 'quantity' => 1]],
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Cửa hàng chỉ nhận đơn từ 08:00 đến 22:00.');
    }

    public function test_checkout_fails_when_below_min_order_value(): void
    {
        StoreSetting::create(array_merge(StoreSetting::defaults(), [
            'is_open' => true,
            'min_order_value' => 100000,
            'opens_at' => '00:00',
            'closes_at' => '23:59',
        ]));

        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'price' => 40000,
            'is_available' => true,
        ]);

        // Subtotal = 40,000 < min_order_value 100,000
        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Đơn Nhỏ',
            'customer_phone' => '0912345678',
            'delivery_address' => '123 Đường Test',
            'items' => [['food_id' => $food->id, 'quantity' => 1]],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
    }

    public function test_checkout_succeeds_when_open_within_hours_and_meeting_min_order(): void
    {
        StoreSetting::create(array_merge(StoreSetting::defaults(), [
            'is_open' => true,
            'opens_at' => '08:00',
            'closes_at' => '22:00',
            'min_order_value' => 50000,
        ]));

        // Mock current time to 12:00 PM (inside hours)
        Carbon::setTestNow(Carbon::parse('2026-10-05 12:00:00'));

        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'price' => 60000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Hợp Lệ',
            'customer_phone' => '0912345678',
            'delivery_address' => '123 Đường Hợp Lệ',
            'items' => [['food_id' => $food->id, 'quantity' => 1]],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.total_price', 60000);
    }
}
