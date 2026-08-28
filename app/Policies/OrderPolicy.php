<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can update the order status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}
