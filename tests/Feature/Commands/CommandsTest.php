<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;

it('orders:cancel-stale cancela solo los pedidos pendientes antiguos', function () {
    $old = Order::factory()->pending()->create(['created_at' => now()->subHours(50)]);
    $recent = Order::factory()->pending()->create();
    $oldConfirmed = Order::factory()->create(['status' => OrderStatus::Confirmed, 'created_at' => now()->subHours(50)]);

    $this->artisan('orders:cancel-stale', ['--hours' => 48])
        ->expectsOutput('Pedidos cancelados: 1')
        ->assertSuccessful();

    expect($old->fresh()->status)->toBe(OrderStatus::Cancelled)
        ->and($recent->fresh()->status)->toBe(OrderStatus::Pending)
        ->and($oldConfirmed->fresh()->status)->toBe(OrderStatus::Confirmed);
});

it('orders:cancel-stale falla si las horas no son válidas', function () {
    $this->artisan('orders:cancel-stale', ['--hours' => 0])->assertFailed();
});

it('catalog:refresh-featured destaca los más vendidos y quita el resto', function () {
    $bestSeller = Product::factory()->create(['is_featured' => false]);
    $oldFeatured = Product::factory()->create(['is_featured' => true]);
    Order::factory()->create(['status' => OrderStatus::Paid])->addProduct($bestSeller, 3);

    $this->artisan('catalog:refresh-featured', ['--limit' => 1])
        ->expectsOutput('Productos destacados: 1')
        ->assertSuccessful();

    expect($bestSeller->fresh()->is_featured)->toBeTrue()
        ->and($oldFeatured->fresh()->is_featured)->toBeFalse();
});

it('catalog:refresh-featured mantiene los destacados si todavía no hay ventas', function () {
    $featured = Product::factory()->create(['is_featured' => true]);

    $this->artisan('catalog:refresh-featured')
        ->expectsOutput('Todavía no hay ventas: se mantienen los destacados actuales.')
        ->assertSuccessful();

    expect($featured->fresh()->is_featured)->toBeTrue();
});

it('products:low-stock lista los productos con poco stock', function () {
    $low = Product::factory()->create(['name' => 'Producto con poco stock', 'stock' => 2]);
    $high = Product::factory()->create(['name' => 'Producto con mucho stock', 'stock' => 40]);

    $this->artisan('products:low-stock', ['--threshold' => 5])
        ->expectsOutputToContain($low->name)
        ->doesntExpectOutputToContain($high->name)
        ->assertSuccessful();
});

it('products:low-stock falla si el umbral no es válido', function () {
    $this->artisan('products:low-stock', ['--threshold' => -1])->assertFailed();
});

it('customers:top lista los clientes que superan el gasto mínimo', function () {
    $big = customer();
    $small = customer();
    Order::factory()->for($big)->create(['status' => OrderStatus::Paid, 'total' => 800]);
    Order::factory()->for($small)->create(['status' => OrderStatus::Paid, 'total' => 50]);

    $this->artisan('customers:top', ['--min' => 500])
        ->expectsOutputToContain($big->email)
        ->doesntExpectOutputToContain($small->email)
        ->assertSuccessful();
});

it('customers:top falla si el gasto mínimo no es un número', function () {
    $this->artisan('customers:top', ['--min' => 'mucho'])->assertFailed();
});

it('el administrador lanza los comandos desde el panel', function () {
    $old = Order::factory()->pending()->create(['created_at' => now()->subHours(50)]);

    $this->actingAs(userWithRole(Role::ADMIN))
        ->post(route('panel.maintenance.cancel-stale-orders'))
        ->assertRedirect()
        ->assertSessionHas('flash.banner', 'Pedidos cancelados: 1');

    expect($old->fresh()->status)->toBe(OrderStatus::Cancelled);
});

it('solo el administrador puede lanzar los comandos desde el panel', function () {
    $this->actingAs(userWithRole(Role::WAREHOUSE))
        ->post(route('panel.maintenance.refresh-featured'))
        ->assertForbidden();
});
