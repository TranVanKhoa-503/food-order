<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\ShipperPayrollService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminShipperController extends Controller
{
    public function __construct(
        protected ShipperPayrollService $payrollService
    ) {}

    /**
     * Display a list of all shippers with performance statistics.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $isActive = $request->query('is_active');

        $query = User::query()->where('role', UserRole::Shipper);

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

        $shippers = $query->withCount([
            'shipperOrders as delivering_count' => fn ($q) => $q->where('status', OrderStatus::Delivering),
            'shipperOrders as completed_this_month_count' => fn ($q) => $q->where('status', OrderStatus::Completed)
                ->whereYear('completed_at', now()->year)
                ->whereMonth('completed_at', now()->month),
            'shipperOrders as total_completed_count' => fn ($q) => $q->where('status', OrderStatus::Completed),
        ])->get()->map(function (User $shipper) {
            $metrics = $this->payrollService->calculate($shipper, now()->year, now()->month);
            $shipper->estimated_salary = $metrics['total_salary'];
            $shipper->estimated_commission = $metrics['total_commission'];

            return $shipper;
        });

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $shippers->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'email' => $s->email,
                    'phone' => $s->phone,
                    'is_active' => $s->is_active,
                    'delivering_count' => $s->delivering_count,
                    'completed_this_month_count' => $s->completed_this_month_count,
                    'total_completed_count' => $s->total_completed_count,
                    'estimated_salary' => $s->estimated_salary,
                    'estimated_commission' => $s->estimated_commission,
                ]),
            ]);
        }

        return view('admin.shippers.index', compact('shippers', 'search', 'isActive'));
    }

    /**
     * Assign a shipper to an order.
     */
    public function assignOrder(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'shipper_id' => ['required', 'exists:users,id'],
        ]);

        $shipper = User::query()->where('role', UserRole::Shipper)->findOrFail($validated['shipper_id']);

        if (! $shipper->is_active) {
            throw new UnprocessableEntityHttpException('Không thể phân công cho shipper đã bị khóa tài khoản.');
        }

        $currentStatus = $order->status instanceof OrderStatus ? $order->status->value : (string) $order->status;
        if (in_array($currentStatus, ['completed', 'cancelled'], true)) {
            throw new UnprocessableEntityHttpException('Không thể phân công shipper cho đơn hàng đã hoàn tất hoặc đã hủy.');
        }

        $order->update([
            'shipper_id' => $shipper->id,
            'assigned_at' => now(),
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Phân công shipper thành công.',
                'data' => new OrderResource($order->fresh(['shipper', 'items'])),
            ]);
        }

        return back()->with('success', "Đã phân công đơn hàng cho shipper {$shipper->name}.");
    }
}
