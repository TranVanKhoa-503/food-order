<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ToggleUserStatusRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all users for admin.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role');
        $isActive = $request->query('is_active');
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = User::query();

        if (! empty($role)) {
            $query->where('role', $role);
        }

        if (! is_null($isActive) && $isActive !== '') {
            $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Display the specified user details.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Toggle active status of a user.
     */
    public function toggleStatus(ToggleUserStatusRequest $request, User $user): UserResource
    {
        if ($user->id === $request->user()->id) {
            throw new UnprocessableEntityHttpException('Không thể tự vô hiệu hóa tài khoản quản trị của chính mình.');
        }

        $user->forceFill([
            'is_active' => (bool) $request->validated('is_active'),
        ])->save();

        return new UserResource($user);
    }
}
