<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\TestResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Tests\TestCase;

class GoogleSignInTest extends TestCase
{
    use RefreshDatabase;

    private const SUBJECT = '109876543210987654321';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'test-client.apps.googleusercontent.com',
            'services.google.client_secret' => 'test-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);
    }

    public function test_login_shows_google_below_the_form_and_requires_configuration_to_use_it(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSeeInOrder([
                'type="submit"', 'Đăng Nhập', '</form>', 'class="auth-google auth-google-after-form"',
                'href="'.route('auth.google.redirect').'"', 'Đăng nhập với Google', 'Chưa có tài khoản?',
            ], false);
        $this->get(route('register'))->assertOk()->assertSee('Đăng ký với Google');

        config(['services.google.client_secret' => null]);
        $this->get(route('login'))
            ->assertOk()
            ->assertSeeInOrder([
                'type="submit"', 'Đăng Nhập', '</form>', 'class="auth-google auth-google-after-form"',
                'type="button" disabled', 'Đăng nhập với Google', 'Đăng nhập Google chưa được cấu hình.',
            ], false)
            ->assertDontSee('href="'.route('auth.google.redirect').'"', false);
        $this->get(route('register'))->assertOk()->assertDontSee('Đăng ký với Google');
        $this->get(route('auth.google.redirect'))->assertRedirect(route('login'))->assertSessionHasErrors('google');
    }

    public function test_new_google_account_blocks_regular_registration_on_web_and_api(): void
    {
        $this->withSession(['url.intended' => route('orders.index')]);
        $state = $this->startGoogleSignIn();
        $this->mockGoogle(['sub' => self::SUBJECT, 'email' => 'Google.User@example.com', 'name' => 'Google Customer', 'email_verified' => true]);

        $this->completeGoogleSignIn($state)->assertRedirect(route('orders.index'));
        $user = User::query()->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(self::SUBJECT, $user->google_id);
        $this->assertSame('google.user@example.com', $user->email);
        $this->assertSame(UserRole::User, $user->role);
        $this->assertNotNull($user->email_verified_at);
        $this->assertArrayNotHasKey('google_id', $user->toArray());
        $this->post(route('logout'))->assertRedirect(route('home'));

        $payload = [
            'name' => 'New Customer', 'email' => 'GOOGLE.USER@EXAMPLE.COM',
            'password' => 'TestPass123!', 'password_confirmation' => 'TestPass123!',
        ];
        $this->from(route('register'))->post(route('register'), $payload)
            ->assertRedirect(route('register'))->assertSessionHasErrors('email');
        $this->postJson('/api/v1/auth/register', $payload)->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_returning_google_user_signs_into_the_same_account_by_google_id(): void
    {
        $user = User::factory()->create(['email' => 'original@example.com', 'google_id' => self::SUBJECT]);
        $state = $this->startGoogleSignIn();
        $this->mockGoogle(['sub' => self::SUBJECT, 'email' => 'new-address@example.com', 'email_verified' => true]);

        $this->completeGoogleSignIn($state)->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('original@example.com', $user->fresh()->email);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_does_not_link_to_an_existing_password_account(): void
    {
        $existing = User::factory()->admin()->create(['email' => 'Existing@Example.com']);
        $state = $this->startGoogleSignIn();
        $this->mockGoogle(['sub' => self::SUBJECT, 'email' => 'existing@example.com', 'email_verified' => true]);

        $this->completeGoogleSignIn($state)->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertNull($existing->fresh()->google_id);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_unverified_google_email_does_not_create_an_account(): void
    {
        $state = $this->startGoogleSignIn();
        $this->mockGoogle(['sub' => self::SUBJECT, 'email' => 'unverified@example.com', 'email_verified' => false]);

        $this->completeGoogleSignIn($state)->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_locked_google_account_stays_locked(): void
    {
        User::factory()->inactive()->create(['email' => 'locked@example.com', 'google_id' => self::SUBJECT]);
        $state = $this->startGoogleSignIn();
        $this->mockGoogle(['sub' => self::SUBJECT, 'email' => 'locked@example.com', 'email_verified' => true]);

        $this->completeGoogleSignIn($state)->assertRedirect(route('login'))->assertSessionHasErrors(['google' => 'Tài khoản đã bị khóa.']);
        $this->assertGuest();
    }

    public function test_expired_or_cancelled_sign_in_returns_to_login(): void
    {
        $state = $this->startGoogleSignIn();
        $this->get(route('auth.google.callback', ['state' => $state, 'error' => 'access_denied']))
            ->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->completeGoogleSignIn($state)->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();
    }

    public function test_google_sign_in_checks_the_session_and_its_expiry(): void
    {
        $this->startGoogleSignIn();
        $this->completeGoogleSignIn('another-session')->assertRedirect(route('login'))
            ->assertSessionHasErrors(['google' => 'Phiên đăng nhập Google không hợp lệ. Vui lòng thử lại.']);

        $state = $this->startGoogleSignIn();
        $this->travel(11)->minutes();
        $this->completeGoogleSignIn($state)->assertRedirect(route('login'))
            ->assertSessionHasErrors(['google' => 'Phiên đăng nhập Google đã hết hạn. Vui lòng thử lại.']);
        $this->assertGuest();
    }

    public function test_google_account_cannot_use_local_password_flows(): void
    {
        Notification::fake();
        $user = User::factory()->create(['google_id' => self::SUBJECT]);
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])->assertUnauthorized();
        $this->postJson('/api/v1/auth/forgot-password', ['email' => $user->email])->assertOk();
        Notification::assertNothingSent();
        $this->postJson('/api/v1/auth/reset-password', [
            'email' => $user->email, 'token' => Password::createToken($user),
            'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!',
        ])->assertUnprocessable();
        $this->actingAs($user)->get(route('profile'))->assertOk()
            ->assertSee('không có mật khẩu riêng trên FoodOrder')->assertDontSee('name="current_password"', false);
        $this->putJson('/api/v1/user/password', [
            'current_password' => 'password', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!',
        ])->assertForbidden();
    }

    private function startGoogleSignIn(): string
    {
        $response = $this->get(route('auth.google.redirect'))->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertSame('accounts.google.com', parse_url($location, PHP_URL_HOST));
        parse_str(parse_url($location, PHP_URL_QUERY), $query);
        $this->assertSame('S256', $query['code_challenge_method']);
        $this->assertSame(config('services.google.redirect'), $query['redirect_uri']);
        $this->assertNotEmpty($query['state']);

        return $query['state'];
    }

    private function completeGoogleSignIn(string $state): TestResponse
    {
        return $this->get(route('auth.google.callback', ['state' => $state, 'code' => 'valid-test-code']));
    }

    private function mockGoogle(array $identity): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], json_encode(['access_token' => 'test-token', 'scope' => 'openid email profile'])),
            new Response(200, [], json_encode($identity)),
        ]));
        Socialite::partialMock()->shouldReceive('buildProvider')
            ->with(GoogleProvider::class, config('services.google'))
            ->once()->andReturnUsing(function ($class, $config) use ($stack) {
                return (new $class(request(), $config['client_id'], $config['client_secret'], $config['redirect']))
                    ->setHttpClient(new Client(['handler' => $stack]));
            });
    }
}
