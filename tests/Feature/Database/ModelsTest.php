<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

it('el precio final usa el descuento si existe', function () {
    expect((float) Product::factory()->create(['price' => 100, 'discount_price' => null])->final_price)->toBe(100.0)
        ->and((float) Product::factory()->state(['price' => 100])->onSale()->create()->final_price)->toBe(80.0);
});

it('el cliente tiene nombre completo', function () {
    $client = Client::factory()->create(['first_name' => 'Ana', 'last_name' => 'López Ruiz']);

    expect($client->full_name)->toBe('Ana López Ruiz');
});

it('el estado del pedido se convierte en enum y un pedido pendiente no está pagado', function () {
    $order = Order::factory()->pending()->create();

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->paid_at)->toBeNull()
        ->and($order->payment_reference)->toBeNull();
});

it('aplica borrado lógico a productos, categorías, pedidos y usuarios', function (string $model) {
    $record = $model::factory()->create();

    $record->delete();

    expect($model::find($record->id))->toBeNull()
        ->and($model::withTrashed()->find($record->id)->trashed())->toBeTrue();
})->with([Product::class, Category::class, Order::class, User::class]);
