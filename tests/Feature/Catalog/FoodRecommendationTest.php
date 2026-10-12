<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_meat_dishes_with_vegetables_are_not_recommended_as_vegetarian(): void
    {
        $category = Category::factory()->create(['name' => 'Món chính', 'slug' => 'mon-chinh']);
        Food::factory()->for($category)->create([
            'name' => 'Salad gà', 'description' => 'Thịt gà ăn kèm rau', 'is_available' => true,
        ]);
        Food::factory()->for($category)->create([
            'name' => 'Bún thịt nướng', 'description' => 'Ăn kèm rau và đậu', 'is_available' => true,
        ]);
        Food::factory()->for($category)->create([
            'name' => 'Cơm chay', 'description' => 'Rau củ và nấm', 'is_available' => true,
        ]);

        $this->postJson('/api/v1/recommendations', ['preference' => 'vegetarian', 'limit' => 6])
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Cơm chay');
    }

    public function test_not_spicy_filter_handles_negation_without_hiding_actual_spicy_ingredients(): void
    {
        $category = Category::factory()->create(['name' => 'Món chính', 'slug' => 'mon-chinh']);
        Food::factory()->for($category)->create([
            'name' => 'Salad gà', 'description' => 'Không cay, không thêm ớt', 'is_available' => true,
        ]);
        Food::factory()->for($category)->create([
            'name' => 'Gà sốt cay', 'description' => 'Có ớt', 'is_available' => true,
        ]);
        Food::factory()->for($category)->create([
            'name' => 'Mì sốt ớt', 'description' => 'Rau không cay nhưng sốt ớt cay', 'is_available' => true,
        ]);

        $this->postJson('/api/v1/recommendations', ['preference' => 'not_spicy', 'limit' => 6])
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Salad gà');
    }

    public function test_public_can_get_available_recommendations_by_budget_and_category(): void
    {
        $main = Category::factory()->create(['slug' => 'mon-chinh', 'name' => 'Món chính']);
        $snack = Category::factory()->create(['slug' => 'khai-vi', 'name' => 'Ăn vặt']);

        Food::factory()->create([
            'category_id' => $main->id,
            'name' => 'Cơm gà',
            'price' => 45000,
            'is_available' => true,
        ]);
        Food::factory()->create([
            'category_id' => $main->id,
            'name' => 'Lẩu đặc biệt',
            'price' => 120000,
            'is_available' => true,
        ]);
        Food::factory()->create([
            'category_id' => $snack->id,
            'name' => 'Bánh tráng',
            'price' => 25000,
            'is_available' => true,
        ]);
        Food::factory()->create([
            'category_id' => $main->id,
            'name' => 'Cơm hết món',
            'price' => 30000,
            'is_available' => false,
        ]);

        $response = $this->postJson('/api/v1/recommendations', [
            'category' => 'mon-chinh',
            'max_price' => 50000,
            'preference' => 'any',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Cơm gà')
            ->assertJsonCount(1, 'data');
    }

    public function test_recommendations_can_filter_vegetarian_foods_and_never_return_unavailable_foods(): void
    {
        $main = Category::factory()->create(['slug' => 'mon-chinh', 'name' => 'Món chính']);
        $vegetarian = Category::factory()->create(['slug' => 'mon-chay', 'name' => 'Món chay']);

        Food::factory()->create([
            'category_id' => $main->id,
            'name' => 'Gà rán cay',
            'description' => 'Món thịt cay',
            'price' => 40000,
            'is_available' => true,
        ]);
        Food::factory()->create([
            'category_id' => $vegetarian->id,
            'name' => 'Salad rau củ',
            'description' => 'Món chay thanh mát',
            'price' => 40000,
            'is_available' => true,
        ]);
        Food::factory()->create([
            'category_id' => $vegetarian->id,
            'name' => 'Cơm chay tạm hết',
            'price' => 30000,
            'is_available' => false,
        ]);

        $response = $this->postJson('/api/v1/recommendations', [
            'preference' => 'vegetarian',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Salad rau củ')
            ->assertJsonCount(1, 'data');
    }

    public function test_recommendations_return_a_helpful_message_when_no_food_matches(): void
    {
        $category = Category::factory()->create(['slug' => 'mon-chinh']);
        Food::factory()->create([
            'category_id' => $category->id,
            'price' => 90000,
            'is_available' => true,
        ]);

        $this->postJson('/api/v1/recommendations', ['max_price' => 10000])
            ->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('message', 'Chưa tìm thấy món phù hợp. Bạn thử nới rộng tiêu chí nhé.');
    }
}
