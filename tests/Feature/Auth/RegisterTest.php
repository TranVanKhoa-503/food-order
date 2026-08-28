<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Tạo Tài Khoản Mới');
    }

    public function test_guest_can_register_via_web_form(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nguyễn Văn Test',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0987654321',
            'address' => '123 Đường ABC, Quận 1',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Nguyễn Văn Test',
            'email' => 'testuser@example.com',
            'phone' => '0987654321',
            'address' => '123 Đường ABC, Quận 1',
            'role' => UserRole::User->value,
            'is_active' => true,
        ]);
    }

    public function test_guest_can_register_via_json_api(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'API User',
            'email' => 'apiuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'API User')
            ->assertJsonPath('data.email', 'apiuser@example.com')
            ->assertJsonPath('data.role', 'user')
            ->assertJsonPath('data.is_active', true);

        $this->assertAuthenticated();
    }

    public function test_client_cannot_register_with_admin_role_or_inactive_flag(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Hacker Admin',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'is_active' => false,
        ])->assertCreated();

        $user = User::query()->where('email', 'hacker@example.com')->firstOrFail();
        $this->assertSame(UserRole::User, $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Duplicate User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_unconfirmed_password(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Mismatch Password',
            'email' => 'mismatch@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Short Pass',
            'email' => 'shortpass@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }
}
