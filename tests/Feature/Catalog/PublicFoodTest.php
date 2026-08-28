<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFoodTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_get_foods_list_with_only_available_items(): void
    {
        $category = Category::factory()->create();
        Food::factory()->count(3)->create(['category_id' => $category->id, 'is_available' => true]);
        Food::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        $response = $this->getJson('/api/v1/foods');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_public_can_filter_foods_by_category_id_and_slug(): void
    {
        $cat1 = Category::factory()->create(['name' => 'Cơm', 'slug' => 'com']);
        $cat2 = Category::factory()->create(['name' => 'Bún', 'slug' => 'bun']);

        Food::factory()->create(['category_id' => $cat1->id, 'name' => 'Cơm Tấm', 'is_available' => true]);
        Food::factory()->create(['category_id' => $cat2->id, 'name' => 'Bún Chả', 'is_available' => true]);

        $response = $this->getJson('/api/v1/foods?category=com');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Cơm Tấm');

        $responseId = $this->getJson('/api/v1/foods?category='.$cat2->id);
        $responseId->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Bún Chả');
    }

    public function test_public_can_search_foods_by_keyword(): void
    {
        $category = Category::factory()->create();
        Food::factory()->create(['category_id' => $category->id, 'name' => 'Phở Bò Tái Lăn', 'is_available' => true]);
        Food::factory()->create(['category_id' => $category->id, 'name' => 'Bánh Mì Kẹp Thịt', 'is_available' => true]);

        $response = $this->getJson('/api/v1/foods?search=Phở');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Phở Bò Tái Lăn');
    }

    public function test_public_can_filter_foods_by_price_range(): void
    {
        $category = Category::factory()->create();
        Food::factory()->create(['category_id' => $category->id, 'name' => 'Món 30k', 'price' => 30000, 'is_available' => true]);
        Food::factory()->create(['category_id' => $category->id, 'name' => 'Món 60k', 'price' => 60000, 'is_available' => true]);
        Food::factory()->create(['category_id' => $category->id, 'name' => 'Món 100k', 'price' => 100000, 'is_available' => true]);

        $response = $this->getJson('/api/v1/foods?min_price=50000&max_price=80000');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Món 60k');
    }

    public function test_public_can_get_available_food_detail(): void
    {
        $category = Category::factory()->create(['name' => 'Món Chính']);
        $food = Food::factory()->create([
            'category_id' => $category->id,
            'name' => 'Gà Rán Giòn',
            'price' => 55000,
            'is_available' => true,
        ]);

        $response = $this->getJson('/api/v1/foods/'.$food->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $food->id)
            ->assertJsonPath('data.name', 'Gà Rán Giòn')
            ->assertJsonPath('data.price', 55000)
            ->assertJsonPath('data.category.name', 'Món Chính');
    }

    public function test_public_cannot_get_unavailable_food_detail(): void
    {
        $food = Food::factory()->create(['is_available' => false]);

        $this->getJson('/api/v1/foods/'.$food->id)
            ->assertNotFound();
    }
}
