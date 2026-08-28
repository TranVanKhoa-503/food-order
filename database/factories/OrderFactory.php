<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(30, 500) * 1000;

        return [
            'order_code' => 'FO-'.fake()->unique()->numerify('############'),
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->numerify('09########'),
            'delivery_address' => fake()->address(),
            'note' => fake()->optional()->sentence(),
            'subtotal' => $subtotal,
            'shipping_fee' => 0,
            'total_price' => $subtotal,
            'payment_method' => PaymentMethod::CashOnDelivery->value,
            'payment_status' => PaymentStatus::Unpaid->value,
            'status' => OrderStatus::Pending->value,
            'cancel_reason' => null,
            'cancelled_at' => null,
            'completed_at' => null,
        ];
    }
}
