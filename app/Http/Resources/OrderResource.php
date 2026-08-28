<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'delivery_address' => $this->delivery_address,
            'note' => $this->note,
            'subtotal' => (int) $this->subtotal,
            'shipping_fee' => (int) $this->shipping_fee,
            'total_price' => (int) $this->total_price,
            'payment_method' => $this->payment_method instanceof PaymentMethod ? $this->payment_method->value : (string) $this->payment_method,
            'payment_status' => $this->payment_status instanceof PaymentStatus ? $this->payment_status->value : (string) $this->payment_status,
            'status' => $this->status instanceof OrderStatus ? $this->status->value : (string) $this->status,
            'cancel_reason' => $this->cancel_reason,
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
