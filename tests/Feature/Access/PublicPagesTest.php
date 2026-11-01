<?php

use App\Models\Product;

it('las páginas públicas se ven sin iniciar sesión', function () {
    $product = Product::factory()->create();

    $this->get('/')->assertOk();
    $this->get('/products')->assertOk()->assertSee($product->name);
    $this->get(route('products.show', $product))->assertOk()->assertSee($product->sku);
});

it('un producto inactivo no se puede ver', function () {
    $product = Product::factory()->inactive()->create();

    $this->get(route('products.show', $product))->assertNotFound();
});
