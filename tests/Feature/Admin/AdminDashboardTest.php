<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Food;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
