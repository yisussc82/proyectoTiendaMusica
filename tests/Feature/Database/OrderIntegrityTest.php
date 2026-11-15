<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;

it('un pedido se lee completo después de retirar el producto y dar de baja al usuario', function () {
    $user = customer();
    $product = Product::factory()->create(['name' => 'Guitarra Les Paul', 'price' => 1200, 'discount_price' => null]);
    $order = Order::factory()->for($user)->create(['shipping_cost' => 5, 'shipping_city' => 'Madrid']);
    $order->addProduct($product, 2);
    $order->recalculateTotal();

    $product->delete();
    $user->delete();

    $order = Order::with(['user', 'items.product'])->find($order->id);
    $line = $order->items->first();

    expect($order->user->email)->toBe($user->email)
        ->and($line->product->name)->toBe('Guitarra Les Paul')
        ->and($line->name_at_sale)->toBe('Guitarra Les Paul')
        ->and((float) $line->price_at_sale)->toBe(1200.0)
        ->and($line->quantity)->toBe(2)
        ->and($order->shipping_city)->toBe('Madrid')
        ->and((float) $order->total)->toBe(2405.0);
});

it('la base de datos no deja borrar del todo un producto vendido', function () {
    $product = Product::factory()->create();
    Order::factory()->create()->addProduct($product);

    expect(fn () => $product->forceDelete())->toThrow(QueryException::class);
});

it('quitar una oferta no cambia el precio de un pedido ya hecho', function () {
    $product = Product::factory()->create(['price' => 100, 'discount_price' => 80]);
    $order = Order::factory()->create(['shipping_cost' => 0]);
    $order->addProduct($product);
    $order->recalculateTotal();

    $product->update(['discount_price' => null]);
    $order->recalculateTotal();

    expect((float) $order->items()->first()->price_at_sale)->toBe(80.0)
        ->and((float) $order->fresh()->total)->toBe(80.0);
});

it('no se puede confirmar un pedido de un producto sin stock', function () {
    $product = Product::factory()->outOfStock()->create();
    $order = Order::factory()->pending()->create();
    $order->addProduct($product);

    expect(fn () => $order->confirm())->toThrow(LogicException::class);

    expect($product->fresh()->stock)->toBe(0)
        ->and($order->fresh()->status)->toBe(OrderStatus::Pending);
});

it('si dos pedidos quieren la última unidad, solo se confirma el primero', function () {
    $product = Product::factory()->create(['stock' => 1]);
    $first = Order::factory()->pending()->create();
    $second = Order::factory()->pending()->create();
    $first->addProduct($product);
    $second->addProduct($product);

    $first->confirm();

    expect(fn () => $second->confirm())->toThrow(LogicException::class);

    expect($product->fresh()->stock)->toBe(0)
        ->and($first->fresh()->status)->toBe(OrderStatus::Confirmed)
        ->and($second->fresh()->status)->toBe(OrderStatus::Pending);
});

it('no se puede añadir una cantidad menor que 1', function (int $quantity) {
    $order = Order::factory()->create();

    expect(fn () => $order->addProduct(Product::factory()->create(), $quantity))
        ->toThrow(InvalidArgumentException::class);

    expect($order->items()->count())->toBe(0);
})->with([0, -3]);

it('añadir otra vez el mismo producto suma la cantidad en la misma línea', function () {
    $product = Product::factory()->create();
    $order = Order::factory()->create();

    $order->addProduct($product, 1);
    $order->addProduct($product, 2);

    expect($order->items()->count())->toBe(1)
        ->and($order->items()->first()->quantity)->toBe(3);
});

it('la base de datos no admite dos líneas del mismo producto en un pedido', function () {
    $product = Product::factory()->create();
    $order = Order::factory()->create();
    $line = ['name_at_sale' => $product->name, 'price_at_sale' => $product->price, 'quantity' => 1];
    $order->products()->attach($product->id, $line);

    expect(fn () => $order->products()->attach($product->id, $line))->toThrow(QueryException::class);
});
