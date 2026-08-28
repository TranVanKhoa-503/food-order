<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Quên Mật Khẩu?');
    }

    public function test_user_can_request_password_reset_link_via_web(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'user@example.com',
        ]);

        $response->assertSessionHas('status');
    }

    public function test_user_can_request_password_reset_link_via_api(): void
    {
        $user = User::factory()->create(['email' => 'user_api@example.com']);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'user_api@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được liên kết đặt lại mật khẩu.');
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $this->get('/reset-password/sample-token?email=user@example.com')
            ->assertOk()
            ->assertSee('Đặt Lại Mật Khẩu');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'resetuser@example.com',
            'password' => 'oldpassword',
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'resetuser@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/login');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_user_can_reset_password_via_json_api(): void
    {
        $user = User::factory()->create([
            'email' => 'reset_api@example.com',
            'password' => 'oldpassword',
        ]);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'reset_api@example.com',
            'password' => 'brandnewpass123',
            'password_confirmation' => 'brandnewpass123',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Đặt lại mật khẩu thành công.');

        $user->refresh();
        $this->assertTrue(Hash::check('brandnewpass123', $user->password));
    }

    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'invalid_token@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'invalid-token-123',
            'email' => 'invalid_token@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }
}
