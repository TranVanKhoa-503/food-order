<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_categories(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/admin/categories')
            ->assertForbidden();
    }

    public function test_admin_can_list_categories_with_search(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Món Chính Đặc Sắc']);
        Category::factory()->create(['name' => 'Trà Sữa Trân Châu']);

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/categories?search=Chính');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Món Chính Đặc Sắc');
    }

    public function test_admin_can_create_category_with_auto_generated_slug(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Món Ăn Vặt Mới',
                'description' => 'Mô tả ăn vặt',
                'icon' => 'fa-bowl-food',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Món Ăn Vặt Mới')
            ->assertJsonPath('data.slug', 'mon-an-vat-moi');

        $this->assertDatabaseHas('categories', [
            'name' => 'Món Ăn Vặt Mới',
            'slug' => 'mon-an-vat-moi',
        ]);
    }

    public function test_admin_cannot_create_category_with_duplicate_slug(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Trà Trái Cây', 'slug' => 'tra-trai-cay']);

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Trà Trái Cây 2',
                'slug' => 'tra-trai-cay',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Tên Cũ', 'slug' => 'ten-cu']);

        $response = $this->actingAs($admin)
            ->putJson('/api/v1/admin/categories/'.$category->id, [
                'name' => 'Tên Mới Cập Nhật',
                'slug' => 'ten-moi-cap-nhat',
                'description' => 'Mô tả mới',
                'icon' => 'fa-leaf',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Tên Mới Cập Nhật')
            ->assertJsonPath('data.slug', 'ten-moi-cap-nhat');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Tên Mới Cập Nhật',
            'slug' => 'ten-moi-cap-nhat',
        ]);
    }

    public function test_admin_cannot_delete_category_with_existing_foods(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        Food::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)
            ->deleteJson('/api/v1/admin/categories/'.$category->id);

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Không thể xóa danh mục đang chứa món ăn.');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_delete_category_without_foods(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson('/api/v1/admin/categories/'.$category->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
