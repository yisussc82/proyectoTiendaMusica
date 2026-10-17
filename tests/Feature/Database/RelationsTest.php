<?php

use App\Models\Category;
use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Role;
use App\Models\User;

it('relaciones 1 a n', function () {
    $category = Category::factory()->has(Product::factory(2))->create();
    $product = Product::factory()->has(ProductImage::factory(3), 'images')->create();
    $client = Client::factory()->has(ClientAddress::factory(2), 'addresses')->create();
    $user = User::factory()->has(Order::factory(2))->create();

    expect($category->products)->toHaveCount(2)
        ->and($product->images)->toHaveCount(3)
        ->and($client->addresses)->toHaveCount(2)
        ->and($user->orders)->toHaveCount(2);
});

it('relación n a n: usuarios y roles', function () {
    $admin = Role::create(['name' => Role::ADMIN]);
    $manager = Role::create(['name' => Role::MANAGER]);
    $user = User::factory()->create();

    $user->roles()->attach([$admin->id, $manager->id]);

    expect($user->roles)->toHaveCount(2)
        ->and($admin->users->first()->is($user))->toBeTrue()
        ->and($user->hasRole(Role::ADMIN))->toBeTrue();
});

it('relación n a n con columnas extra en la pivote: pedidos y productos', function () {
    $guitar = Product::factory()->create(['price' => 100, 'discount_price' => null]);
    $cable = Product::factory()->create(['price' => 15, 'discount_price' => null]);
    $order = Order::factory()->create(['shipping_cost' => 5]);

    // Se rellenan las columnas extra desde el código
    $order->addProduct($guitar, 2);
    $order->addProduct($cable, 1);
    $order->recalculateTotal();

    $pivot = $order->products()->find($guitar->id)->pivot;

    expect($pivot->name_at_sale)->toBe($guitar->name)
        ->and((float) $pivot->price_at_sale)->toBe(100.0)
        ->and((int) $pivot->quantity)->toBe(2)
        ->and((float) $order->total)->toBe(220.0); // 200 + 15 + 5 de envío
});
