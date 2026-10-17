<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\QueryException;

// ---------- Pedido ↔ Producto (order_items: name_at_sale, price_at_sale, quantity) ----------

it('al añadir un producto al pedido se guardan nombre, precio y cantidad en la pivote', function () {
    $product = Product::factory()->create(['price' => 100, 'discount_price' => 80]);
    $order = Order::factory()->create();

    $order->addProduct($product, 2);

    $pivot = $order->products()->first()->pivot;
    expect($pivot->name_at_sale)->toBe($product->name)
        ->and((float) $pivot->price_at_sale)->toBe(80.0) // usa el precio con descuento
        ->and((int) $pivot->quantity)->toBe(2);
});

it('calcula el total del pedido a partir de la pivote', function () {
    $order = Order::factory()->create(['shipping_cost' => 5]);
    $order->addProduct(Product::factory()->create(['price' => 100, 'discount_price' => null]), 2);
    $order->addProduct(Product::factory()->create(['price' => 30, 'discount_price' => null]), 1);

    $order->recalculateTotal();

    expect((float) $order->total)->toBe(235.0); // 200 + 30 + 5 de envío
});

it('cambiar el precio de un producto no altera los pedidos ya hechos', function () {
    $product = Product::factory()->create(['price' => 50, 'discount_price' => null]);
    $order = Order::factory()->create();
    $order->addProduct($product);

    $product->update(['price' => 999]);

    expect((float) $order->items()->first()->price_at_sale)->toBe(50.0);
});

it('un pedido sigue mostrando productos borrados (soft delete)', function () {
    $product = Product::factory()->create();
    $order = Order::factory()->create();
    $order->addProduct($product);

    $product->delete();

    expect($order->products)->toHaveCount(1)
        ->and($order->items()->first()->product)->not->toBeNull();
});

// ---------- Carrito (cart_items: quantity) ----------

it('gestiona la cantidad del carrito desde la pivote', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $user->cart()->attach($product->id, ['quantity' => 1]);
    $user->cart()->updateExistingPivot($product->id, ['quantity' => 3]);

    expect((int) $user->cart()->first()->pivot->quantity)->toBe(3);

    $user->cart()->detach($product->id);

    expect($user->cart()->count())->toBe(0);
});

it('no permite el mismo producto dos veces en el carrito', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $user->cart()->attach($product->id);

    expect(fn () => $user->cart()->attach($product->id))->toThrow(QueryException::class);
});

// ---------- Lista de deseos (product_user: notify_on_discount) ----------

it('filtra por la columna extra de la lista de deseos', function () {
    $product = Product::factory()->create();
    [$wantsNotice, $doesNot] = User::factory(2)->create();

    $wantsNotice->wishlist()->attach($product->id, ['notify_on_discount' => true]);
    $doesNot->wishlist()->attach($product->id, ['notify_on_discount' => false]);

    $toNotify = $product->wishlistedBy()->wherePivot('notify_on_discount', true)->get();

    expect($product->wishlistedBy)->toHaveCount(2)
        ->and($toNotify)->toHaveCount(1)
        ->and($toNotify->first()->is($wantsNotice))->toBeTrue();
});
