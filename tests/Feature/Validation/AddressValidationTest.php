<?php

use App\Models\ClientAddress;

it('una dirección sin los campos obligatorios no se guarda', function () {
    $this->actingAs(customer())
        ->post(route('account.addresses.store'), [])
        ->assertSessionHasErrors(['address_line_1', 'city', 'province', 'postal_code']);

    expect(ClientAddress::count())->toBe(0);
});

it('el código postal debe corresponder a la provincia', function () {
    $this->actingAs(customer())
        ->post(route('account.addresses.store'), [
            'address_line_1' => 'Calle Mayor 1',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postal_code' => '08001',
        ])
        ->assertSessionHasErrors(['postal_code']);
});

it('un cliente crea una dirección válida y queda como predeterminada', function () {
    $user = customer();
    $old = ClientAddress::factory()->for($user->client)->default()->create();

    $this->actingAs($user)
        ->post(route('account.addresses.store'), [
            'address_line_1' => 'Calle Mayor 1',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postal_code' => '28013',
            'is_default' => '1',
        ])
        ->assertRedirect(route('account.addresses.index'));

    $new = $user->client->addresses()->where('postal_code', '28013')->first();

    expect($new->is_default)->toBeTrue()
        ->and($old->fresh()->is_default)->toBeFalse();
});
