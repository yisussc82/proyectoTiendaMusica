<?php

use App\Models\User;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;
use Livewire\Livewire;

test('user accounts can be deleted', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser');

    // User usa SoftDeletes: la cuenta queda borrada lógicamente, no desaparece de la tabla
    expect($user->fresh()->trashed())->toBeTrue();
})->skip(function () {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

test('correct password must be provided before account can be deleted', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'wrong-password')
        ->call('deleteUser')
        ->assertHasErrors(['password']);

    expect($user->fresh()->trashed())->toBeFalse();
})->skip(function () {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');
