<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderStatusService;

it('recorre el ciclo completo de un pedido', function () {
    $product = Product::factory()->create(['stock' => 5]);
    $order = Order::factory()->pending()->create();
    $order->addProduct($product, 2);
    $service = app(OrderStatusService::class);

    $service->change($order, OrderStatus::Confirmed);
    expect($product->fresh()->stock)->toBe(3);

    $service->change($order, OrderStatus::Paid);
    expect($order->fresh()->paid_at)->not->toBeNull();

    $service->change($order, OrderStatus::Preparing);
    $service->change($order, OrderStatus::Shipped);
    $service->change($order, OrderStatus::Delivered);

    expect($order->fresh()->status)->toBe(OrderStatus::Delivered);
});

it('no permite saltarse estados', function () {
    $order = Order::factory()->pending()->create();

    expect(fn () => app(OrderStatusService::class)->change($order, OrderStatus::Shipped))
        ->toThrow(LogicException::class);

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
});

it('un pedido entregado ya no se puede cancelar', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Delivered]);

    expect(fn () => app(OrderStatusService::class)->change($order, OrderStatus::Cancelled))
        ->toThrow(LogicException::class);
});

it('no confirma un pedido si falta stock', function () {
    $product = Product::factory()->create(['stock' => 1]);
    $order = Order::factory()->pending()->create();
    $order->addProduct($product, 3);

    expect(fn () => app(OrderStatusService::class)->change($order, OrderStatus::Confirmed))
        ->toThrow(LogicException::class);

    expect($order->fresh()->status)->toBe(OrderStatus::Pending)
        ->and($product->fresh()->stock)->toBe(1);
});
