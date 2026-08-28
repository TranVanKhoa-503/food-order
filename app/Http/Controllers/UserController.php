<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View|UserResource
    {
        /** @var User $user */
        $user = $request->user();

        if ($request->expectsJson() || $request->is('api/*')) {
            return new UserResource($user);
        }

        return view('user.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return (new UserResource($user))->response()->setStatusCode(200);
        }

        return back()->with('status', 'Cập nhật thông tin thành công!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): Response|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        $user->forceFill([
            'password' => $validated['password'],
        ])->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->noContent();
        }

        return back()->with('password_status', 'Đổi mật khẩu thành công!');
    }
}
