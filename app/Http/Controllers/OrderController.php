<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\OrderStatusService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated user.
     */
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $status = $request->query('status');
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = $request->user()->orders()->with('items');

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate($perPage);

        if ($request->expectsJson() || $request->is('api/*')) {
            return OrderResource::collection($orders);
        }

        return view('orders.index', compact('orders', 'status'));
    }

    /**
     * Create a new order via checkout (authenticated customer).
     */
    public function store(CreateOrderRequest $request, CheckoutService $checkoutService): JsonResponse
    {
        $order = $checkoutService->checkout($request->user(), $request->validated());

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified order details for the owner.
     */
    public function show(Request $request, Order $order): View|OrderResource
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->isAdmin(), 404, 'Không tìm thấy đơn hàng.');

        $order->load(['items', 'user']);

        if ($request->expectsJson() || $request->is('api/*')) {
            return new OrderResource($order);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel own pending order by customer.
     */
    public function cancel(CancelOrderRequest $request, Order $order, OrderStatusService $orderStatusService): OrderResource
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->isAdmin(), 404, 'Không tìm thấy đơn hàng.');

        $cancelledOrder = $orderStatusService->cancelByUser($order, $request->validated('reason'));

        return new OrderResource($cancelledOrder);
    }
}
