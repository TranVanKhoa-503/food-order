<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
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
}
