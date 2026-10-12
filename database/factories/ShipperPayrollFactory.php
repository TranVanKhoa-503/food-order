<?php

namespace Database\Factories;

use App\Models\ShipperPayroll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipperPayroll>
 */
class ShipperPayrollFactory extends Factory
{
    public function definition(): array
    {
        $completedOrdersCount = fake()->numberBetween(10, 100);
        $totalShippingFee = $completedOrdersCount * 25000;
        $totalCommission = round($totalShippingFee * 0.5);
        $baseSalary = 2000000;

        return [
            'shipper_id' => User::factory()->shipper(),
            'year' => now()->year,
            'month' => now()->month,
            'base_salary' => $baseSalary,
            'commission_rate' => 0.50,
            'completed_orders_count' => $completedOrdersCount,
            'total_shipping_fee' => $totalShippingFee,
            'total_commission' => $totalCommission,
            'total_salary' => $baseSalary + $totalCommission,
            'status' => 'draft',
            'finalized_at' => null,
            'finalized_by' => null,
            'paid_at' => null,
            'paid_by' => null,
            'note' => fake()->optional()->sentence(),
        ];
    }
}
