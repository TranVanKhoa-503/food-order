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
            'delivery_zone_id' => $this->delivery_zone_id,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'delivery_address' => $this->delivery_address,
            'delivery_zone_name' => $this->delivery_zone_name,
            'note' => $this->note,
            'subtotal' => (int) $this->subtotal,
            'discount_amount' => (int) $this->discount_amount,
            'voucher' => $this->whenLoaded('voucher', fn () => $this->voucher ? [
                'id' => $this->voucher->id,
                'code' => $this->voucher->code,
            ] : null),
            'shipping_fee' => (int) $this->shipping_fee,
            'total_price' => (int) $this->total_price,
            'payment_method' => $this->payment_method instanceof PaymentMethod ? $this->payment_method->value : (string) $this->payment_method,
            'payment_status' => $this->payment_status instanceof PaymentStatus ? $this->payment_status->value : (string) $this->payment_status,
            'status' => $this->status instanceof OrderStatus ? $this->status->value : (string) $this->status,
            'cancel_reason' => $this->cancel_reason,
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'status_history' => $this->whenLoaded('statusHistories', fn () => $this->statusHistories->map(fn ($history) => [
                'id' => $history->id,
                'from_status' => $history->from_status,
                'to_status' => $history->to_status,
                'reason' => $history->reason,
                'actor' => $history->actor?->name,
                'created_at' => $history->created_at?->toISOString(),
            ])),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
