<?php

namespace Database\Factories;

use App\Models\OperatingExpense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OperatingExpense>
 */
class OperatingExpenseFactory extends Factory
{
    public function definition(): array
    {
        $categories = array_keys(OperatingExpense::CATEGORIES);

        return [
            'category' => fake()->randomElement($categories),
            'amount' => fake()->numberBetween(50, 5000) * 1000,
            'expense_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'description' => fake()->sentence(),
            'created_by' => User::factory()->admin(),
        ];
    }
}
