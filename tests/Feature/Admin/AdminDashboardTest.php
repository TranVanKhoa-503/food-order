<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_view_dashboard_metrics(): void
    {
        $admin = User::factory()->admin()->create();

        $users = User::factory()->count(4)->create(); // 4 regular users
        Food::factory()->count(6)->create();

        Order::factory()->for($users[0])->create([
            'status' => OrderStatus::Completed,
            'total_price' => 150000,
        ]);
        Order::factory()->for($users[1])->create([
            'status' => OrderStatus::Completed,
            'total_price' => 200000,
        ]);
        Order::factory()->for($users[2])->create([
            'status' => OrderStatus::Pending,
            'total_price' => 100000,
        ]);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard');

        $response->assertOk()
            ->assertJsonPath('data.total_revenue', 350000)
            ->assertJsonPath('data.total_orders', 3)
            ->assertJsonPath('data.pending_orders', 1)
            ->assertJsonPath('data.total_users', 4)
            ->assertJsonPath('data.total_foods', 6)
            ->assertJsonCount(3, 'data.recent_orders');
    }

    public function test_dashboard_returns_top_foods_and_daily_revenue(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $order = Order::factory()->for($customer)->create([
            'status' => OrderStatus::Completed,
            'total_price' => 80000,
        ]);
        OrderItem::factory()->for($order)->create([
            'food_name' => 'Cơm gà bán chạy',
            'quantity' => 2,
            'line_total' => 80000,
        ]);

        $this->actingAs($admin)->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('data.top_foods.0.food_name', 'Cơm gà bán chạy')
            ->assertJsonPath('data.top_foods.0.quantity', 2)
            ->assertJsonPath('data.daily_revenue.'.now()->toDateString(), 80000);
    }

    public function test_daily_revenue_query_is_aggregated_and_not_queried_per_day(): void
    {
        $admin = User::factory()->admin()->create();

        DB::enableQueryLog();

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard');
        $response->assertOk();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Continuous 7-day range is returned
        $dailyRevenue = $response->json('data.daily_revenue');
        $this->assertCount(7, $dailyRevenue);

        // Verify that daily revenue uses a single aggregated group-by query rather than N loop queries
        $dailyQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'daily_total') || str_contains($q['query'], 'order_date');
        });
        $this->assertCount(1, $dailyQueries);
    }
}
