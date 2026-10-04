<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('auth-login');
        RateLimiter::clear('auth-register');
        RateLimiter::clear('auth-forgot-password');
        RateLimiter::clear('order-checkout');
    }

    public function test_login_rate_limiting_blocks_after_excessive_attempts(): void
    {
        $user = User::factory()->create(['password' => bcrypt('CorrectPassword123')]);

        // Attempt 10 failed logins
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'WrongPassword',
            ]);
            $this->assertNotSame(429, $response->status(), "Attempt {$i} should not be throttled yet");
        }

        // 11th attempt should receive 429 Too Many Requests
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'WrongPassword',
        ]);
        $response->assertStatus(429);
    }

    public function test_register_rate_limiting_blocks_after_excessive_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/v1/auth/register', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'phone' => '091234567'.$i,
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]);
            $this->assertNotSame(429, $response->status());
        }

        // 6th attempt from same IP should receive 429
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Excessive User',
            'email' => 'excessive@example.com',
            'phone' => '0912345679',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertStatus(429);
    }

    public function test_forgot_password_rate_limiting_blocks_after_excessive_attempts(): void
    {
        $email = 'target@example.com';

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/v1/auth/forgot-password', [
                'email' => $email,
            ]);
            $this->assertNotSame(429, $response->status());
        }

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $email,
        ]);
        $response->assertStatus(429);
    }

    public function test_checkout_rate_limiting_blocks_after_excessive_attempts(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($user)->postJson('/api/v1/orders', [
                'customer_name' => 'Khách Đặt Hàng',
                'customer_phone' => '0901234567',
                'delivery_address' => 'Địa chỉ test',
                'items' => [], // validation error (422), but rate limit still applies
            ]);
            $this->assertNotSame(429, $response->status());
        }

        // 11th checkout attempt by the same user within 1 minute
        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'customer_name' => 'Khách Đặt Hàng',
            'customer_phone' => '0901234567',
            'delivery_address' => 'Địa chỉ test',
            'items' => [],
        ]);
        $response->assertStatus(429);
    }
}
