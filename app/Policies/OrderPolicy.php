<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::WAREHOUSE, Role::SUPPORT);
    }

    public function view(User $user, Order $order): bool
    {
        return $order->user_id === $user->id || $this->viewAny($user);
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasRole(Role::ADMIN, Role::WAREHOUSE);
    }

    public function cancel(User $user, Order $order): bool
    {
        if (in_array($order->status, [OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::Cancelled])) {
            return false;
        }

        if ($user->hasRole(Role::ADMIN, Role::SUPPORT)) {
            return true;
        }

        return $order->user_id === $user->id
            && in_array($order->status, [OrderStatus::Pending, OrderStatus::Confirmed]);
    }
}
