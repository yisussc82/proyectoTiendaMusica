<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Role;

it('la fecha hasta no puede ser anterior a la fecha desde', function () {
    $this->actingAs(userWithRole(Role::ADMIN))
        ->get(route('panel.orders.index', ['from' => '2026-10-20', 'to' => '2026-10-01']))
        ->assertSessionHasErrors(['to']);
});

it('el filtro por estado solo muestra los pedidos de ese estado', function () {
    $paid = Order::factory()->create(['status' => OrderStatus::Paid]);
    $cancelled = Order::factory()->create(['status' => OrderStatus::Cancelled]);

    $this->actingAs(userWithRole(Role::ADMIN))
        ->get(route('panel.orders.index', ['status' => 'paid']))
        ->assertOk()
        ->assertSee($paid->order_number)
        ->assertDontSee($cancelled->order_number);
});
