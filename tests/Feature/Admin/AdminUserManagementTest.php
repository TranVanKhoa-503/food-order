<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/v1/admin/users')
            ->assertForbidden();
    }

    public function test_admin_can_list_users_with_search_and_filter(): void
    {
        $admin = User::factory()->admin()->create();

        User::factory()->create([
            'name' => 'Nguyễn Tìm Kiếm',
            'email' => 'findme@example.com',
            'is_active' => true,
        ]);
        User::factory()->create([
            'name' => 'Người Khác',
            'email' => 'other@example.com',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/users?search=findme');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'findme@example.com');

        $responseActive = $this->actingAs($admin)->getJson('/api/v1/admin/users?is_active=0');
        $responseActive->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'other@example.com');
    }

    public function test_admin_can_view_user_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['name' => 'Chi Tiết User']);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/users/'.$user->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', 'Chi Tiết User');
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patchJson('/api/v1/admin/users/'.$user->id.'/status', [
            'is_active' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.is_active', false);

        $user->refresh();
        $this->assertFalse($user->is_active);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patchJson('/api/v1/admin/users/'.$admin->id.'/status', [
            'is_active' => false,
        ]);

        $response->assertUnprocessable();

        $admin->refresh();
        $this->assertTrue($admin->is_active);
    }
}
