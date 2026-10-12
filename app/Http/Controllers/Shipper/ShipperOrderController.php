<?php

namespace App\Http\Controllers\Shipper;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipper\UpdateShipperOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ShipperOrderController extends Controller
{
    /**
     * Display a listing of orders for shippers.
     */
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $user = $request->user();
        $status = $request->query('status', 'delivering');
        $search = trim((string) $request->query('search', ''));
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = Order::with(['items', 'user', 'deliveryZone'])->latest();

        if ($user && $user->isShipper()) {
            $query->where(function ($q) use ($user) {
                $q->where('shipper_id', $user->id)
                    ->orWhere(fn ($unassigned) => $unassigned->whereNull('shipper_id')->where('status', OrderStatus::Delivering));
            });
        }

        if ($status !== 'all') {
            if ($status === 'completed_today') {
                $query->where('status', OrderStatus::Completed)
                    ->whereDate('completed_at', today());
            } elseif ($status === 'cancelled_today') {
                $query->where('status', OrderStatus::Cancelled)
                    ->whereDate('cancelled_at', today());
            } elseif (in_array($status, ['delivering', 'completed', 'cancelled'], true)) {
                $query->where('status', $status);
            }
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('delivery_address', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate($perPage)->withQueryString();

        $deliveringCountQuery = Order::query()->where('status', OrderStatus::Delivering);
        $completedTodayCountQuery = Order::query()->where('status', OrderStatus::Completed)->whereDate('completed_at', today());
        $cancelledTodayCountQuery = Order::query()->where('status', OrderStatus::Cancelled)->whereDate('cancelled_at', today());

        if ($user && $user->isShipper()) {
            $filterShipper = fn ($q) => $q->where(fn ($sub) => $sub->where('shipper_id', $user->id)
                ->orWhere(fn ($unassigned) => $unassigned->whereNull('shipper_id')->where('status', OrderStatus::Delivering)));
            $deliveringCountQuery->where($filterShipper);
            $completedTodayCountQuery->where($filterShipper);
            $cancelledTodayCountQuery->where($filterShipper);
        }

        $deliveringCount = $deliveringCountQuery->count();
        $completedTodayCount = $completedTodayCountQuery->count();
        $cancelledTodayCount = $cancelledTodayCountQuery->count();

        if ($request->expectsJson() || $request->is('api/*')) {
            return OrderResource::collection($orders);
        }

        return view('shipper.orders', compact(
            'orders',
            'status',
            'search',
            'deliveringCount',
            'completedTodayCount',
            'cancelledTodayCount',
        ));
    }

    /**
     * Display the specified order details for shipper.
     */
    public function show(Request $request, Order $order): View|OrderResource
    {
        abort_unless($request->user()->can('view', $order), 404, 'Không tìm thấy đơn hàng.');

        $order->load(['items', 'user', 'statusHistories.actor', 'deliveryZone']);

        if ($request->expectsJson() || $request->is('api/*')) {
            return new OrderResource($order);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Confirm delivery success or failure for an order.
     */
    public function updateStatus(
        UpdateShipperOrderStatusRequest $request,
        Order $order,
        OrderStatusService $orderStatusService,
    ): OrderResource|JsonResponse|RedirectResponse {
        $user = $request->user();
        $targetStatus = OrderStatus::from($request->validated('status'));
        $reason = $request->validated('reason');

        try {
            $updatedOrder = DB::transaction(function () use ($order, $user, $targetStatus, $reason, $orderStatusService) {
                // Read assignment and status under the same lock as the update.
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

                if ($user->isShipper() && $lockedOrder->shipper_id && $lockedOrder->shipper_id !== $user->id) {
                    abort(403, 'Đơn hàng này đã được phân công cho shipper khác.');
                }

                if ($lockedOrder->status !== OrderStatus::Delivering) {
                    $statusLabel = match ($lockedOrder->status) {
                        OrderStatus::Pending => 'chờ xác nhận',
                        OrderStatus::Confirmed => 'đã xác nhận',
                        OrderStatus::Completed => 'giao thành công',
                        OrderStatus::Cancelled => 'đã hủy',
                        default => $lockedOrder->status->value,
                    };

                    throw new UnprocessableEntityHttpException(
                        "Đơn hàng #{$lockedOrder->order_code} hiện ở trạng thái {$statusLabel}. Chỉ có thể xác nhận kết quả khi đơn đang giao hàng."
                    );
                }

                if ($user->isShipper() && ! $lockedOrder->shipper_id) {
                    $lockedOrder->update(['shipper_id' => $user->id, 'assigned_at' => now()]);
                }

                return $orderStatusService->transition($lockedOrder, $targetStatus, $reason, $user);
            });
        } catch (ConflictHttpException|UnprocessableEntityHttpException $exception) {
            if ($request->expectsJson() || $request->is('api/*')) {
                throw $exception;
            }

            $request->session()->forget('success');

            return redirect()->route('shipper.orders.index')->with('error', $exception->getMessage());
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return new OrderResource($updatedOrder);
        }

        $statusLabel = $targetStatus === OrderStatus::Completed ? 'thành công' : 'thất bại';

        return back()->with('success', "Đơn hàng #{$order->order_code} đã được xác nhận giao {$statusLabel}!");
    }
}
