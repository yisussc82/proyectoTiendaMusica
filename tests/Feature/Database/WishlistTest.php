<?php

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;

it('se cambia notify_on_discount de la lista de deseos y el cambio queda guardado', function () {
    $user = customer();
    $product = Product::factory()->create();
    $user->wishlist()->attach($product->id, ['notify_on_discount' => false]);

    $user->wishlist()->updateExistingPivot($product->id, ['notify_on_discount' => true]);

    $this->assertDatabaseHas('product_user', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'notify_on_discount' => true,
    ]);

    expect((bool) User::find($user->id)->wishlist()->first()->pivot->notify_on_discount)->toBeTrue();
});

it('la lista de deseos no admite el mismo producto dos veces', function () {
    $user = customer();
    $product = Product::factory()->create();
    $user->wishlist()->attach($product->id);

    expect(fn () => $user->wishlist()->attach($product->id))->toThrow(QueryException::class);

    $user->wishlist()->syncWithoutDetaching([$product->id]);

    expect($user->wishlist()->count())->toBe(1);
});

it('un usuario no puede tener el mismo rol dos veces', function () {
    $user = userWithRole(Role::ADMIN);

    expect(fn () => $user->roles()->attach(Role::where('name', Role::ADMIN)->first()))
        ->toThrow(QueryException::class);
});
