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
        ->and(Product::count())->toBe(72)
        ->and(User::count())->toBe(25)
        ->and(Order::count())->toBeGreaterThan(0);
});
