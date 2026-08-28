<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_checkout(): void
    {
        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Vãng Lai',
            'customer_phone' => '0900000000',
            'delivery_address' => 'Địa chỉ test',
            'items' => [
                ['food_id' => 1, 'quantity' => 1],
            ],
        ]);

        $response->assertUnauthorized();
    }

    public function test_inactive_user_cannot_checkout(): void
    {
        $user = User::factory()->inactive()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'User Bị Khóa',
            'customer_phone' => '0900000000',
            'delivery_address' => 'Địa chỉ test',
            'items' => [
                ['food_id' => 1, 'quantity' => 1],
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_customer_can_checkout_with_single_item(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'name' => 'Phở Bò Tái',
            'price' => 65000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Nguyễn Văn A',
            'customer_phone' => '0912345678',
            'delivery_address' => '123 Phố Huế, Hà Nội',
            'note' => 'Không bỏ hành lá',
            'items' => [
                [
                    'food_id' => $food->id,
                    'quantity' => 2,
                    'note' => 'Ăn liền',
                ],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.customer_name', 'Nguyễn Văn A')
            ->assertJsonPath('data.subtotal', 130000)
            ->assertJsonPath('data.shipping_fee', 0)
            ->assertJsonPath('data.total_price', 130000)
            ->assertJsonPath('data.payment_method', 'cod')
            ->assertJsonPath('data.payment_status', 'unpaid')
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.food_name', 'Phở Bò Tái')
            ->assertJsonPath('data.items.0.unit_price', 65000)
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.items.0.line_total', 130000);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'customer_name' => 'Nguyễn Văn A',
            'subtotal' => 130000,
            'total_price' => 130000,
            'status' => OrderStatus::Pending->value,
            'payment_method' => PaymentMethod::CashOnDelivery->value,
            'payment_status' => PaymentStatus::Unpaid->value,
        ]);

        $this->assertDatabaseHas('order_items', [
            'food_id' => $food->id,
            'food_name' => 'Phở Bò Tái',
            'unit_price' => 65000,
            'quantity' => 2,
            'line_total' => 130000,
        ]);
    }

    public function test_customer_can_checkout_with_multiple_items(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food1 = Food::factory()->create(['category_id' => $category->id, 'name' => 'Cơm Tấm', 'price' => 60000, 'is_available' => true]);
        $food2 = Food::factory()->create(['category_id' => $category->id, 'name' => 'Trà Sữa', 'price' => 30000, 'is_available' => true]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Trần Thị B',
            'customer_phone' => '0987654321',
            'delivery_address' => '456 Lê Lợi, TP.HCM',
            'items' => [
                ['food_id' => $food1->id, 'quantity' => 2], // 120,000
                ['food_id' => $food2->id, 'quantity' => 3], // 90,000
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.subtotal', 210000)
            ->assertJsonPath('data.total_price', 210000)
            ->assertJsonCount(2, 'data.items');

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 2);
    }

    public function test_backend_calculates_price_strictly_from_database(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'name' => 'Món Đắt Tiền',
            'price' => 100000, // Giá DB là 100k
            'is_available' => true,
        ]);

        // Client cố tình gửi giá giả 1,000đ và tổng giả
        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Hacker Giá',
            'customer_phone' => '0911223344',
            'delivery_address' => 'Địa chỉ',
            'subtotal' => 2000,
            'total_price' => 2000,
            'items' => [
                [
                    'food_id' => $food->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'line_total' => 2000,
                ],
            ],
        ]);

        // Backend phải tự tính đúng 200,000đ
        $response->assertCreated()
            ->assertJsonPath('data.subtotal', 200000)
            ->assertJsonPath('data.total_price', 200000)
            ->assertJsonPath('data.items.0.unit_price', 100000)
            ->assertJsonPath('data.items.0.line_total', 200000);
    }

    public function test_checkout_fails_and_rolls_back_if_any_food_is_unavailable(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $foodAvailable = Food::factory()->create(['category_id' => $category->id, 'is_available' => true]);
        $foodUnavailable = Food::factory()->create(['category_id' => $category->id, 'name' => 'Món Hết Hàng', 'is_available' => false]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Hàng',
            'customer_phone' => '0911223344',
            'delivery_address' => 'Địa chỉ',
            'items' => [
                ['food_id' => $foodAvailable->id, 'quantity' => 1],
                ['food_id' => $foodUnavailable->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('message', "Món ăn 'Món Hết Hàng' hiện đã tạm hết hoặc ngừng phục vụ.");

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_checkout_fails_with_non_existent_food(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Hàng',
            'customer_phone' => '0911223344',
            'delivery_address' => 'Địa chỉ',
            'items' => [
                ['food_id' => 99999, 'quantity' => 1],
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_fails_with_empty_items_or_invalid_quantity(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Hàng',
            'customer_phone' => '0911223344',
            'delivery_address' => 'Địa chỉ',
            'items' => [],
        ])->assertUnprocessable()->assertJsonValidationErrors(['items']);

        $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Hàng',
            'customer_phone' => '0911223344',
            'delivery_address' => 'Địa chỉ',
            'items' => [
                ['food_id' => 1, 'quantity' => 0],
            ],
        ])->assertUnprocessable()->assertJsonValidationErrors(['items.0.quantity']);
    }

    public function test_duplicate_food_ids_in_request_are_merged_accurately(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'name' => 'Gà Nướng',
            'price' => 50000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Gộp',
            'customer_phone' => '0911223344',
            'delivery_address' => 'Địa chỉ',
            'items' => [
                ['food_id' => $food->id, 'quantity' => 2, 'note' => 'Cay ít'],
                ['food_id' => $food->id, 'quantity' => 3, 'note' => 'Thêm tương'],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.quantity', 5)
            ->assertJsonPath('data.items.0.line_total', 250000)
            ->assertJsonPath('data.total_price', 250000);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }
}
