<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Food;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Get aggregate statistics for admin dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $totalRevenue = (int) Order::query()
            ->where('status', OrderStatus::Completed)
            ->sum('total_price');

        $totalOrders = Order::query()->count();
        $todayOrders = Order::query()->whereDate('created_at', today())->count();
        $pendingOrders = Order::query()->where('status', OrderStatus::Pending)->count();

        $statusCounts = [];
        foreach (OrderStatus::cases() as $statusCase) {
            $statusCounts[$statusCase->value] = Order::query()->where('status', $statusCase)->count();
        }

        $totalUsers = User::query()->where('role', UserRole::User)->count();
        $totalFoods = Food::query()->count();
        $recentOrders = Order::query()->with(['items', 'user'])->latest()->take(5)->get();

        return response()->json([
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'today_orders' => $todayOrders,
                'pending_orders' => $pendingOrders,
                'orders_by_status' => $statusCounts,
                'total_users' => $totalUsers,
                'total_foods' => $totalFoods,
                'recent_orders' => OrderResource::collection($recentOrders),
            ],
        ]);
    }
}
