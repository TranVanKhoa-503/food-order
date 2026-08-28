<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFoodCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_foods(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/admin/foods')
            ->assertForbidden();
    }

    public function test_admin_can_list_all_foods_including_unavailable(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        Food::factory()->count(2)->create(['category_id' => $category->id, 'is_available' => true]);
        Food::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/foods');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_create_food(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/admin/foods', [
                'category_id' => $category->id,
                'name' => 'Bún Bò Huế Đặc Biệt',
                'description' => 'Chả cua, giò heo, thịt bò',
                'price' => 65000,
                'image' => 'https://example.com/bunbo.jpg',
                'is_available' => true,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Bún Bò Huế Đặc Biệt')
            ->assertJsonPath('data.price', 65000)
            ->assertJsonPath('data.is_available', true);

        $this->assertDatabaseHas('foods', [
            'category_id' => $category->id,
            'name' => 'Bún Bò Huế Đặc Biệt',
            'price' => 65000,
            'is_available' => true,
        ]);
    }

    public function test_admin_can_update_food(): void
    {
        $admin = User::factory()->admin()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $food = Food::factory()->create(['category_id' => $category1->id, 'name' => 'Tên Cũ']);

        $response = $this->actingAs($admin)
            ->putJson('/api/v1/admin/foods/'.$food->id, [
                'category_id' => $category2->id,
                'name' => 'Tên Mới Sau Update',
                'description' => 'Mô tả mới',
                'price' => 70000,
                'image' => 'https://example.com/new.jpg',
                'is_available' => true,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Tên Mới Sau Update')
            ->assertJsonPath('data.price', 70000);

        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'category_id' => $category2->id,
            'name' => 'Tên Mới Sau Update',
            'price' => 70000,
        ]);
    }

    public function test_admin_can_toggle_food_availability(): void
    {
        $admin = User::factory()->admin()->create();
        $food = Food::factory()->create(['is_available' => true]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/v1/admin/foods/'.$food->id.'/availability', [
                'is_available' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.is_available', false);

        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'is_available' => false,
        ]);
    }
}
