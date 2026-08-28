<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(20, 200) * 1000,
            'image' => fake()->imageUrl(600, 400, 'food'),
            'is_available' => true,
        ];
    }
}
