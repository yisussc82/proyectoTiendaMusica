<?php

use App\Enums\OrderStatus;
use App\Models\ClientAddress;
use App\Models\Order;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;

it('un cliente no puede editar ni borrar la dirección de otro cliente', function () {
    $address = ClientAddress::factory()->create();

    $this->actingAs(customer());

    $this->get(route('account.addresses.edit', $address))->assertForbidden();
    $this->put(route('account.addresses.update', $address), [])->assertForbidden();
    $this->delete(route('account.addresses.destroy', $address))->assertForbidden();
});

it('no se puede borrar la dirección predeterminada', function () {
    $user = customer();
    $default = ClientAddress::factory()->for($user->client)->default()->create();
    $other = ClientAddress::factory()->for($user->client)->create();

    $this->actingAs($user);

    $this->delete(route('account.addresses.destroy', $default))->assertForbidden();
    $this->delete(route('account.addresses.destroy', $other))->assertRedirect(route('account.addresses.index'));
    expect(ClientAddress::find($other->id))->toBeNull();
});

it('un cliente solo puede ver sus propios pedidos', function () {
    $user = customer();
    $own = Order::factory()->for($user)->create();
    $other = Order::factory()->create();

    $this->actingAs($user);

    $this->get(route('account.orders.show', $own))->assertOk();
    $this->get(route('account.orders.show', $other))->assertForbidden();
});

it('solo administración, almacén y atención al cliente ven los pedidos del panel', function (string $role, int $status) {
    $this->actingAs(userWithRole($role))
        ->get(route('panel.orders.index'))
        ->assertStatus($status);
})->with([
    [Role::ADMIN, 200],
    [Role::WAREHOUSE, 200],
    [Role::SUPPORT, 200],
    [Role::CATALOG, 403],
]);

it('un cliente cancela su pedido pendiente pero no uno enviado', function () {
    $user = customer();
    $pending = Order::factory()->for($user)->create(['status' => OrderStatus::Pending]);
    $shipped = Order::factory()->for($user)->create(['status' => OrderStatus::Shipped]);

    $this->actingAs($user);

    $this->post(route('account.orders.cancel', $pending))->assertRedirect();
    expect($pending->fresh()->status)->toBe(OrderStatus::Cancelled);

    $this->post(route('account.orders.cancel', $shipped))->assertForbidden();
});

it('solo administración y almacén pueden cambiar el estado de un pedido', function () {
    $order = Order::factory()->create();

    expect(Gate::forUser(userWithRole(Role::WAREHOUSE))->allows('updateStatus', $order))->toBeTrue()
        ->and(Gate::forUser(userWithRole(Role::ADMIN))->allows('updateStatus', $order))->toBeTrue()
        ->and(Gate::forUser(userWithRole(Role::SUPPORT))->allows('updateStatus', $order))->toBeFalse();
});
