<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the application registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $user = User::forceCreate([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => UserRole::User,
            'is_active' => true,
        ]);

        Auth::login($user);

        if ($request->expectsJson() || $request->is('api/*')) {
            return (new UserResource($user))
                ->response()
                ->setStatusCode(201);
        }

        return redirect()->intended(route('home'))
            ->with('status', 'Đăng ký tài khoản thành công!');
    }
}
