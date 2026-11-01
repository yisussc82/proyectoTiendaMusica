<?php

use App\Models\Role;
use App\Models\User;
use Laravel\Fortify\Features;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

test('al registrarse se crea el usuario, su ficha de cliente y el rol customer', function () {
    $response = $this->post('/register', [
        'first_name' => 'Ana',
        'last_name' => 'López Ruiz',
        'email' => 'ana@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'ana@example.com')->first();
    expect($user->name)->toBe('Ana López Ruiz')
        ->and($user->client->full_name)->toBe('Ana López Ruiz')
        ->and($user->hasRole(Role::CUSTOMER))->toBeTrue();
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('el registro exige nombre y apellidos', function () {
    $response = $this->post('/register', [
        'email' => 'ana@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['first_name', 'last_name']);
    $this->assertGuest();
});
