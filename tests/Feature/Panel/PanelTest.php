<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Services\OrderStatusService;

it('almacén avanza el estado de un pedido desde el panel', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Paid]);

    $this->actingAs(userWithRole(Role::WAREHOUSE))
        ->patch(route('panel.orders.status', $order), ['status' => 'preparing'])
        ->assertRedirect();

    expect($order->fresh()->status)->toBe(OrderStatus::Preparing);
});

it('atención al cliente no puede cambiar el estado de un pedido', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Paid]);

    $this->actingAs(userWithRole(Role::SUPPORT))
        ->patch(route('panel.orders.status', $order), ['status' => 'preparing'])
        ->assertForbidden();
});

it('un cambio de estado no permitido muestra un error y no cambia nada', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Paid]);

    $this->actingAs(userWithRole(Role::WAREHOUSE))
        ->patch(route('panel.orders.status', $order), ['status' => 'delivered'])
        ->assertSessionHas('flash.bannerStyle', 'danger');

    expect($order->fresh()->status)->toBe(OrderStatus::Paid);
});

it('un estado que no existe no pasa la validación', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Paid]);

    $this->actingAs(userWithRole(Role::WAREHOUSE))
        ->patch(route('panel.orders.status', $order), ['status' => 'perdido'])
        ->assertSessionHasErrors('status');
});

it('atención al cliente cancela un pedido pagado y se repone el stock', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $order = Order::factory()->pending()->create();
    $order->addProduct($product, 4);
    $service = app(OrderStatusService::class);
    $service->change($order, OrderStatus::Confirmed);
    $service->change($order, OrderStatus::Paid);

    $this->actingAs(userWithRole(Role::SUPPORT))
        ->post(route('panel.orders.cancel', $order))
        ->assertRedirect();

    expect($product->fresh()->stock)->toBe(10)
        ->and($order->fresh()->status)->toBe(OrderStatus::Cancelled);
});

it('el listado de pedidos sigue mostrando los de clientes que borraron su cuenta', function () {
    $user = customer();
    $order = Order::factory()->for($user)->create();
    $user->delete();

    $this->actingAs(userWithRole(Role::ADMIN))
        ->get(route('panel.orders.index'))
        ->assertOk()
        ->assertSee($order->order_number);
});

it('atención al cliente ve los clientes y los filtra por gasto', function () {
    $big = customer();
    $small = customer();
    Order::factory()->for($big)->create(['status' => OrderStatus::Paid, 'total' => 800]);
    Order::factory()->for($small)->create(['status' => OrderStatus::Paid, 'total' => 50]);

    $this->actingAs(userWithRole(Role::SUPPORT))
        ->get(route('panel.clients.index', ['min_spent' => 500]))
        ->assertOk()
        ->assertSee($big->email)
        ->assertDontSee($small->email);
});

it('catálogo no puede ver los clientes', function () {
    $this->actingAs(userWithRole(Role::CATALOG))
        ->get(route('panel.clients.index'))
        ->assertForbidden();
});

it('un gasto mínimo negativo no es válido', function () {
    $this->actingAs(userWithRole(Role::ADMIN))
        ->get(route('panel.clients.index', ['min_spent' => -5]))
        ->assertSessionHasErrors('min_spent');
});
