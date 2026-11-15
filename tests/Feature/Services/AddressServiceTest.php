<?php

use App\Models\ClientAddress;
use App\Services\AddressService;

function addressData(array $overrides = []): array
{
    return array_merge([
        'address_line_1' => 'Calle Mayor 1',
        'city' => 'Madrid',
        'province' => 'Madrid',
        'postal_code' => '28013',
        'is_default' => false,
    ], $overrides);
}

it('la primera dirección de un cliente queda como predeterminada aunque no se marque', function () {
    $client = customer()->client;

    $address = app(AddressService::class)->save($client, addressData());

    expect($address->fresh()->is_default)->toBeTrue();
});

it('marcar una dirección como predeterminada desmarca las demás', function () {
    $client = customer()->client;
    $first = ClientAddress::factory()->for($client)->default()->create();

    $second = app(AddressService::class)->save($client, addressData(['is_default' => true]));

    expect($second->fresh()->is_default)->toBeTrue()
        ->and($first->fresh()->is_default)->toBeFalse();
});

it('siempre queda una dirección predeterminada', function () {
    $client = customer()->client;
    $only = ClientAddress::factory()->for($client)->default()->create();

    app(AddressService::class)->save($client, ['is_default' => false], $only);

    expect($only->fresh()->is_default)->toBeTrue();
});
