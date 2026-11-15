<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;

it('la base de datos se reconstruye y se puebla desde cero', function () {
    $this->seed();

    expect(Role::count())->toBe(5)
        ->and(Category::count())->toBe(17)
        ->and(Product::withTrashed()->count())->toBe(72)
        ->and(Product::onlyTrashed()->count())->toBe(2)
        ->and(Product::where('is_active', false)->count())->toBe(3)
        ->and(Product::onSale()->count())->toBeGreaterThanOrEqual(6)
        ->and(User::count())->toBe(25)
        ->and(Order::count())->toBeGreaterThan(0);
});

it('el cliente de prueba tiene ejemplos de cada caso', function () {
    $this->seed();

    $customer = User::where('email', 'customer@musicstore.test')->first();

    expect($customer->orders()->where('status', 'pending')->exists())->toBeTrue()
        ->and($customer->orders()->where('status', 'paid')->exists())->toBeTrue()
        ->and($customer->wishlist()->wherePivot('notify_on_discount', true)->exists())->toBeTrue();
});
