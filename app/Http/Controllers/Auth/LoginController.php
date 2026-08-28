<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(LoginRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $remember = (bool) ($validated['remember'] ?? false);

        if (! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $remember)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Email hoặc mật khẩu không chính xác.',
                ], 401);
            }

            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không chính xác.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Tài khoản đã bị khóa.',
                ], 403);
            }

            throw ValidationException::withMessages([
                'email' => ['Tài khoản đã bị khóa.'],
            ]);
        }

        $request->session()->regenerate();

        if ($request->expectsJson() || $request->is('api/*')) {
            return (new UserResource($user))->response()->setStatusCode(200);
        }

        return redirect()->intended(route('home'))
            ->with('status', 'Đăng nhập thành công!');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): Response|RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->noContent();
        }

        return redirect()->route('home')
            ->with('status', 'Đã đăng xuất thành công.');
    }

    /**
     * Get the authenticated user profile.
     */
    public function me(Request $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        return new UserResource($user);
    }
}
