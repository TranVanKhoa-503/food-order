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
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ShipperOrderController extends Controller
{
    /**
     * Display a listing of orders for shippers.
     */
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $status = $request->query('status', 'delivering');
        $search = trim((string) $request->query('search', ''));
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = Order::with(['items', 'user', 'deliveryZone'])->latest();

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

        $deliveringCount = Order::query()->where('status', OrderStatus::Delivering)->count();
        $completedTodayCount = Order::query()->where('status', OrderStatus::Completed)->whereDate('completed_at', today())->count();
        $cancelledTodayCount = Order::query()->where('status', OrderStatus::Cancelled)->whereDate('cancelled_at', today())->count();

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
        $currentStatus = $order->status instanceof OrderStatus ? $order->status->value : (string) $order->status;

        if ($currentStatus !== 'delivering') {
            throw new UnprocessableEntityHttpException(
                "Shipper chỉ có thể cập nhật trạng thái đơn hàng khi đang ở trạng thái 'Đang giao hàng' (delivering). Trạng thái hiện tại: '{$currentStatus}'."
            );
        }

        $targetStatus = OrderStatus::from($request->validated('status'));
        $reason = $request->validated('reason');

        $updatedOrder = $orderStatusService->transition(
            $order,
            $targetStatus,
            $reason,
            $request->user(),
        );

        if ($request->expectsJson() || $request->is('api/*')) {
            return new OrderResource($updatedOrder);
        }

        $statusLabel = $targetStatus === OrderStatus::Completed ? 'thành công' : 'thất bại';

        return back()->with('success', "Đơn hàng #{$order->order_code} đã được xác nhận giao {$statusLabel}!");
    }
}
