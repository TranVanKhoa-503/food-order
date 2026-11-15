<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class GoogleSignInController extends Controller
{
    private const STARTED_AT = 'google_oauth_started_at';

    public static function configured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

    public function redirect(Request $request): RedirectResponse
    {
        if (! self::configured()) {
            return $this->error('Đăng nhập với Google chưa được cấu hình.');
        }

        $request->session()->put(self::STARTED_AT, now()->timestamp);

        return $this->provider()->with(['prompt' => 'select_account'])->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! self::configured()) {
            return $this->error('Đăng nhập với Google chưa được cấu hình.');
        }

        $startedAt = $request->session()->pull(self::STARTED_AT);
        $code = $request->query('code');
        if (! is_int($startedAt) || $startedAt < now()->subMinutes(10)->timestamp) {
            $request->session()->forget(['state', 'code_verifier']);

            return $this->error('Phiên đăng nhập Google đã hết hạn. Vui lòng thử lại.');
        }

        if (! is_string($code) || $code === '' || $request->query('error') !== null) {
            $request->session()->forget(['state', 'code_verifier']);

            return $this->error('Đăng nhập Google chưa hoàn tất. Vui lòng thử lại.');
        }

        try {
            $identity = $this->provider()->user();
        } catch (InvalidStateException) {
            $request->session()->forget('code_verifier');

            return $this->error('Phiên đăng nhập Google không hợp lệ. Vui lòng thử lại.');
        } catch (Throwable $exception) {
            Log::warning('Google sign-in provider request failed.', ['type' => $exception::class]);

            return $this->error('Không thể kết nối với Google. Vui lòng thử lại sau.');
        }

        $googleId = $identity->getId();
        $verified = $identity->getRaw()['email_verified'] ?? null;
        $email = Str::lower(trim((string) $identity->getEmail()));

        if (! is_string($googleId) || $googleId === '' || strlen($googleId) > 255
            || ! in_array($verified, [true, 'true'], true)
            || strlen($email) > 255 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Google không cung cấp địa chỉ email đã xác minh cho tài khoản này.');
        }

        $user = User::query()->where('google_id', $googleId)->first();
        if ($user === null) {
            if (User::query()->withEmail($email)->exists()) {
                return $this->error('Email này đã thuộc một tài khoản FoodOrder. Vui lòng đăng nhập bằng phương thức ban đầu.');
            }

            try {
                $name = trim((string) $identity->getName());
                $user = User::forceCreate([
                    'name' => Str::substr($name !== '' ? $name : Str::before($email, '@'), 0, 255),
                    'email' => $email,
                    'email_verified_at' => now(),
                    'google_id' => $googleId,
                    'password' => Str::random(64),
                    'role' => UserRole::User,
                    'is_active' => true,
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                // A second request may have created the account between lookup and insert.
                $user = User::query()->where('google_id', $googleId)->first();
                if ($user === null) {
                    if (User::query()->withEmail($email)->exists()) {
                        return $this->error('Email này đã thuộc một tài khoản FoodOrder. Vui lòng đăng nhập bằng phương thức ban đầu.');
                    }

                    throw $exception;
                }
            }
        }

        if (! $user->is_active) {
            return $this->error('Tài khoản đã bị khóa.');
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', 'Đăng nhập với Google thành công!');
    }

    private function provider(): GoogleProvider
    {
        /** @var GoogleProvider $provider */
        $provider = Socialite::buildProvider(GoogleProvider::class, config('services.google'));

        return $provider->enablePKCE();
    }

    private function error(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['google' => $message]);
    }
}
