<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_get_all_categories(): void
    {
        $cat1 = Category::factory()->create(['name' => 'Món Chính', 'slug' => 'mon-chinh']);
        $cat2 = Category::factory()->create(['name' => 'Đồ Uống', 'slug' => 'do-uong']);

        Food::factory()->count(3)->create(['category_id' => $cat1->id, 'is_available' => true]);
        Food::factory()->create(['category_id' => $cat1->id, 'is_available' => false]);

        $response = $this->getJson('/api/v1/categories?with_foods_count=1');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.slug', 'mon-chinh')
            ->assertJsonPath('data.0.foods_count', 3);
    }

    public function test_public_can_get_category_by_slug(): void
    {
        $category = Category::factory()->create([
            'name' => 'Khai Vị',
            'slug' => 'khai-vi',
            'description' => 'Món khai vị hấp dẫn',
        ]);

        $response = $this->getJson('/api/v1/categories/khai-vi');

        $response->assertOk()
            ->assertJsonPath('data.name', 'Khai Vị')
            ->assertJsonPath('data.slug', 'khai-vi')
            ->assertJsonPath('data.description', 'Món khai vị hấp dẫn');
    }

    public function test_get_non_existent_category_returns_404(): void
    {
        $this->getJson('/api/v1/categories/khong-ton-tai')
            ->assertNotFound();
    }
}
