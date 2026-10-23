<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;

it('descuenta el stock al confirmar y lo repone una sola vez al cancelar', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $order = Order::factory()->pending()->create();
    $order->addProduct($product, 3);

    $order->confirm();
    expect($product->fresh()->stock)->toBe(7)
        ->and($order->status)->toBe(OrderStatus::Confirmed);

    $order->cancel();
    $order->cancel(); // la segunda cancelación no repone otra vez
    expect($product->fresh()->stock)->toBe(10)
        ->and($order->status)->toBe(OrderStatus::Cancelled);
});

it('no descuenta nada si falta stock de algún producto', function () {
    $withStock = Product::factory()->create(['stock' => 5]);
    $withoutStock = Product::factory()->create(['stock' => 1]);
    $order = Order::factory()->pending()->create();
    $order->addProduct($withStock, 2);
    $order->addProduct($withoutStock, 3);

    expect(fn () => $order->confirm())->toThrow(LogicException::class);

    expect($withStock->fresh()->stock)->toBe(5) // la transacción deshace todo
        ->and($order->fresh()->status)->toBe(OrderStatus::Pending);
});
