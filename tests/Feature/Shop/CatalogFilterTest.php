<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;

it('el catálogo filtra por búsqueda, categoría, oferta y stock', function () {
    $guitars = Category::factory()->create();
    $electric = Category::factory()->create(['parent_id' => $guitars->id]);
    $drums = Category::factory()->create();
    Product::factory()->for($electric)->create(['name' => 'Guitarra Telecaster', 'stock' => 3, 'discount_price' => null]);
    Product::factory()->for($drums)->create(['name' => 'Caja Ludwig', 'stock' => 0, 'discount_price' => null]);
    Product::factory()->for($drums)->state(['name' => 'Platos en oferta', 'price' => 200, 'stock' => 5])->onSale()->create();

    $this->get(route('products.index', ['search' => 'telecaster']))
        ->assertSee('Guitarra Telecaster')->assertDontSee('Caja Ludwig');

    $this->get(route('products.index', ['category' => $guitars->slug]))
        ->assertSee('Guitarra Telecaster')->assertDontSee('Caja Ludwig');

    $this->get(route('products.index', ['in_stock' => 1]))
        ->assertSee('Guitarra Telecaster')->assertDontSee('Caja Ludwig');

    $this->get(route('products.index', ['on_sale' => 1]))
        ->assertSee('Platos en oferta')->assertDontSee('Guitarra Telecaster');
});

it('una categoría que no existe no pasa la validación', function () {
    $this->get(route('products.index', ['category' => 'no-existe']))
        ->assertSessionHasErrors('category');
});

it('la portada muestra los más vendidos', function () {
    $product = Product::factory()->create(['name' => 'Bajo Precision']);
    Order::factory()->create(['status' => OrderStatus::Delivered])->addProduct($product, 2);

    $this->get('/')->assertOk()->assertSee('Best sellers')->assertSee('Bajo Precision');
});

it('el personal de catálogo puede previsualizar un producto inactivo', function () {
    $product = Product::factory()->inactive()->create();

    $this->actingAs(userWithRole(Role::CATALOG))
        ->get(route('products.show', $product))
        ->assertOk();
});
