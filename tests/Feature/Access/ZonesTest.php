<?php

use App\Models\Role;

it('un invitado que entra en una zona privada va al inicio de sesión', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with(['/dashboard', '/account', '/panel']);

it('después de iniciar sesión cada usuario va a su zona', function () {
    $this->actingAs(userWithRole(Role::CUSTOMER))
        ->get('/dashboard')
        ->assertRedirect(route('account.dashboard'));

    $this->actingAs(userWithRole(Role::WAREHOUSE))
        ->get('/dashboard')
        ->assertRedirect(route('panel.dashboard'));
});

it('un cliente no puede entrar en el panel', function () {
    $this->actingAs(userWithRole(Role::CUSTOMER))
        ->get('/panel')
        ->assertForbidden();
});

it('el personal entra en el panel pero no en la cuenta de cliente', function (string $role) {
    $staff = userWithRole($role);

    $this->actingAs($staff)->get('/panel')->assertOk();
    $this->actingAs($staff)->get('/account')->assertForbidden();
})->with([Role::ADMIN, Role::CATALOG, Role::WAREHOUSE, Role::SUPPORT]);
