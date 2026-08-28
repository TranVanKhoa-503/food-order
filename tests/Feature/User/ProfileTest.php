<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile_page(): void
    {
        $this->get('/profile')
            ->assertRedirect('/login');

        $this->getJson('/api/v1/user/profile')
            ->assertUnauthorized();
    }

    public function test_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Nguyễn Văn View',
            'email' => 'view@example.com',
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Nguyễn Văn View')
            ->assertSee('view@example.com');
    }

    public function test_user_can_update_profile_via_web(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'phone' => '0111111111',
            'address' => 'Old Address',
        ]);

        $response = $this->actingAs($user)
            ->put('/profile', [
                'name' => 'New Name Updated',
                'phone' => '0999999999',
                'address' => 'New Address 456',
            ]);

        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertSame('New Name Updated', $user->name);
        $this->assertSame('0999999999', $user->phone);
        $this->assertSame('New Address 456', $user->address);
    }

    public function test_user_can_update_profile_via_json_api(): void
    {
        $user = User::factory()->create([
            'name' => 'API Old Name',
        ]);

        $response = $this->actingAs($user)
            ->putJson('/api/v1/user/profile', [
                'name' => 'API New Name',
                'phone' => '0988776655',
                'address' => 'Da Nang',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'API New Name')
            ->assertJsonPath('data.phone', '0988776655')
            ->assertJsonPath('data.address', 'Da Nang');

        $user->refresh();
        $this->assertSame('API New Name', $user->name);
    }

    public function test_user_cannot_update_email_role_or_active_status_via_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'original@example.com',
            'role' => UserRole::User,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->putJson('/api/v1/user/profile', [
                'name' => 'Legit Name',
                'email' => 'newemail@example.com',
                'role' => 'admin',
                'is_active' => false,
            ])->assertOk();

        $user->refresh();
        $this->assertSame('Legit Name', $user->name);
        $this->assertSame('original@example.com', $user->email);
        $this->assertSame(UserRole::User, $user->role);
        $this->assertTrue($user->is_active);
    }
}
