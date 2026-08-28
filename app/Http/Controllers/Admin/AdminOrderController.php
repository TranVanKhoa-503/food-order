<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of all orders for admin.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = Order::with(['items', 'user']);

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if (! empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }

        if (! empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order): OrderResource
    {
        return new OrderResource($order->load(['items', 'user']));
    }

    /**
     * Update the status of an order through the state machine.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): OrderResource
    {
        $targetStatus = OrderStatus::from($request->validated('status'));
        $reason = $request->validated('reason');

        $updatedOrder = $orderStatusService->transition($order, $targetStatus, $reason);

        return new OrderResource($updatedOrder);
    }
}
