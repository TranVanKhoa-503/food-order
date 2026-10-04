<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly string $fromStatus,
        private readonly string $toStatus,
        private readonly ?string $reason = null,
    ) {}

    /**
     * Store the notification in the database so the customer can see it
     * from any device after signing in again.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $status = OrderStatus::tryFrom($this->toStatus);

        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
            'status_label' => $this->statusLabel($status),
            'message' => sprintf('Đơn hàng %s đã chuyển sang trạng thái %s.', $this->order->order_code, $this->statusLabel($status)),
            'reason' => $this->reason,
        ];
    }

    private function statusLabel(?OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::Pending => 'Chờ xác nhận',
            OrderStatus::Confirmed => 'Đã xác nhận',
            OrderStatus::Preparing => 'Đang chế biến',
            OrderStatus::Delivering => 'Đang giao hàng',
            OrderStatus::Completed => 'Hoàn tất',
            OrderStatus::Cancelled => 'Đã hủy',
            default => $this->toStatus,
        };
    }
}
