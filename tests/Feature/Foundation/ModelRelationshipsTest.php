<?php

namespace Tests\Feature\Foundation;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_model_relationships_and_casts_are_available(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $food = Food::factory()->for($category)->create(['price' => 65000]);
        $order = Order::factory()->for($user)->create();
        $item = OrderItem::factory()
            ->for($order)
            ->for($food)
            ->create([
                'food_name' => $food->name,
                'unit_price' => 65000,
                'quantity' => 2,
                'line_total' => 130000,
            ]);

        $this->assertTrue($user->orders->contains($order));
        $this->assertTrue($category->foods->contains($food));
        $this->assertTrue($food->orderItems->contains($item));
        $this->assertTrue($order->items->contains($item));
        $this->assertTrue($order->user->is($user));
        $this->assertTrue($item->order->is($order));
        $this->assertTrue($item->food->is($food));

        $this->assertSame(UserRole::User, $user->role);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame(PaymentMethod::CashOnDelivery, $order->payment_method);
        $this->assertSame(PaymentStatus::Unpaid, $order->payment_status);
        $this->assertSame('65000', $food->price);
        $this->assertSame('130000', $item->line_total);
    }

    public function test_deleting_a_food_keeps_the_order_item_snapshot(): void
    {
        $food = Food::factory()->create();
        $item = OrderItem::factory()
            ->for($food)
            ->create([
                'food_name' => 'Snapshot món ăn',
                'unit_price' => 45000,
                'quantity' => 2,
                'line_total' => 90000,
            ]);

        $food->delete();
        $item->refresh();

        $this->assertNull($item->food_id);
        $this->assertSame('Snapshot món ăn', $item->food_name);
        $this->assertSame('45000', $item->unit_price);
        $this->assertSame('90000', $item->line_total);
    }
}
