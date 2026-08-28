<?php

namespace Tests\Feature\Frontend;

use App\Models\Category;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPagesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_database_categories_and_foods(): void
    {
        $category = Category::factory()->create(['name' => 'Món Ăn Nổi Bật']);
        Food::factory()->create([
            'category_id' => $category->id,
            'name' => 'Bún Đậu Mắm Tôm Đặc Biệt',
            'price' => 75000,
            'is_available' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Món Ăn Nổi Bật')
            ->assertSee('Bún Đậu Mắm Tôm Đặc Biệt')
            ->assertSee('75.000 ₫');
    }

    public function test_customer_can_view_orders_page_via_web(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'order_code' => 'FO-WEB-12345',
            'total_price' => 120000,
        ]);
        OrderItem::factory()->for($order)->create(['food_name' => 'Phở Bò Gầu']);

        $response = $this->actingAs($user)->get('/orders');

        $response->assertOk()
            ->assertSee('Lịch Sử Đơn Hàng')
            ->assertSee('FO-WEB-12345')
            ->assertSee('Phở Bò Gầu');
    }

    public function test_admin_can_access_all_admin_web_views(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Tráng Miệng']);
        Food::factory()->create(['category_id' => $category->id, 'name' => 'Chè Thái']);
        $order = Order::factory()->create(['order_code' => 'FO-ADMIN-VIEW']);
        OrderItem::factory()->for($order)->create();

        // 1. Dashboard
        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Tổng Quan Hoạt Động Cửa Hàng');

        // 2. Orders
        $this->actingAs($admin)->get('/admin/orders')
            ->assertOk()
            ->assertSee('Danh Sách & Xử Lý Đơn Hàng')
            ->assertSee('FO-ADMIN-VIEW');

        // 3. Foods
        $this->actingAs($admin)->get('/admin/foods')
            ->assertOk()
            ->assertSee('Danh Sách Món Ăn Trong Thực Đơn')
            ->assertSee('Chè Thái');

        // 4. Categories
        $this->actingAs($admin)->get('/admin/categories')
            ->assertOk()
            ->assertSee('Danh Mục Món Ăn')
            ->assertSee('Tráng Miệng');

        // 5. Users
        $this->actingAs($admin)->get('/admin/users')
            ->assertOk()
            ->assertSee('Danh Sách Người Dùng Hệ Thống');
    }

    public function test_regular_user_is_forbidden_from_admin_web_views(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($user)->get('/admin/orders')->assertForbidden();
        $this->actingAs($user)->get('/admin/foods')->assertForbidden();
        $this->actingAs($user)->get('/admin/categories')->assertForbidden();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }
}
