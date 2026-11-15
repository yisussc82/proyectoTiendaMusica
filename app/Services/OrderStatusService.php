<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use LogicException;

class OrderStatusService
{
    public function change(Order $order, OrderStatus $newStatus): void
    {
        if (! $order->status->canChangeTo($newStatus)) {
            throw new LogicException("No se puede pasar un pedido de «{$order->status->value}» a «{$newStatus->value}».");
        }

        match ($newStatus) {
            OrderStatus::Confirmed => $order->confirm(),
            OrderStatus::Cancelled => $order->cancel(),
            OrderStatus::Paid => $order->update(['status' => $newStatus, 'paid_at' => now()]),
            default => $order->update(['status' => $newStatus]),
        };
    }

    public function cancelStale(int $hours): int
    {
        $orders = Order::where('status', OrderStatus::Pending)
            ->where('created_at', '<', now()->subHours($hours))
            ->get();

        foreach ($orders as $order) {
            $this->change($order, OrderStatus::Cancelled);
        }

        return $orders->count();
    }
}
