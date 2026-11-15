<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;

it('active, inStock y onSale filtran los productos', function () {
    $normal = Product::factory()->create(['stock' => 3, 'discount_price' => null]);
    $inactive = Product::factory()->inactive()->create(['discount_price' => null]);
    $noStock = Product::factory()->outOfStock()->create(['discount_price' => null]);
    $onSale = Product::factory()->state(['price' => 100, 'stock' => 2])->onSale()->create();

    expect(Product::active()->pluck('id'))->toContain($normal->id)->not->toContain($inactive->id)
        ->and(Product::inStock()->pluck('id'))->toContain($normal->id)->not->toContain($noStock->id)
        ->and(Product::onSale()->pluck('id')->all())->toBe([$onSale->id]);
});

it('search busca por nombre, marca, SKU y nombre de categoría', function () {
    $category = Category::factory()->create(['name' => 'Ukeleles', 'slug' => 'ukeleles']);
    $byName = Product::factory()->create(['name' => 'Guitarra Stratocaster']);
    $byBrand = Product::factory()->create(['brand' => 'Zildjian']);
    $bySku = Product::factory()->create(['sku' => 'TEST-SKU-1']);
    $byCategory = Product::factory()->for($category)->create();

    expect(Product::search('strato')->pluck('id')->all())->toBe([$byName->id])
        ->and(Product::search('zildjian')->pluck('id')->all())->toBe([$byBrand->id])
        ->and(Product::search('TEST-SKU')->pluck('id')->all())->toBe([$bySku->id])
        ->and(Product::search('ukelele')->pluck('id')->all())->toBe([$byCategory->id])
        ->and(Product::search(null)->count())->toBe(Product::count());
});

it('inCategoryTree incluye los productos de la categoría y de sus subcategorías', function () {
    $guitars = Category::factory()->create();
    $electric = Category::factory()->create(['parent_id' => $guitars->id]);
    $drums = Category::factory()->create();
    $inParent = Product::factory()->for($guitars)->create();
    $inChild = Product::factory()->for($electric)->create();
    Product::factory()->for($drums)->create();

    expect(Product::inCategoryTree($guitars)->pluck('id')->all())->toEqualCanonicalizing([$inParent->id, $inChild->id])
        ->and(Product::inCategoryTree($electric)->pluck('id')->all())->toBe([$inChild->id]);
});

it('bestSelling ordena por unidades vendidas e ignora los pedidos cancelados', function () {
    [$top, $second, $onlyCancelled] = Product::factory(3)->create();
    Product::factory()->create();

    $paid = Order::factory()->create(['status' => OrderStatus::Paid]);
    $paid->addProduct($top, 5);
    $paid->addProduct($second, 2);

    $cancelled = Order::factory()->create(['status' => OrderStatus::Cancelled]);
    $cancelled->addProduct($onlyCancelled, 10);
    $cancelled->addProduct($second, 10);

    $result = Product::bestSelling()->get();

    expect($result->pluck('id')->all())->toBe([$top->id, $second->id])
        ->and((int) $result->first()->units_sold)->toBe(5)
        ->and((int) $result->last()->units_sold)->toBe(2);
});

it('spentAtLeast suma lo gastado por cada cliente sin contar los pedidos cancelados', function () {
    $big = customer();
    $small = customer();
    Order::factory()->for($big)->create(['status' => OrderStatus::Paid, 'total' => 400]);
    Order::factory()->for($big)->create(['status' => OrderStatus::Delivered, 'total' => 200]);
    Order::factory()->for($small)->create(['status' => OrderStatus::Paid, 'total' => 100]);
    Order::factory()->for($small)->create(['status' => OrderStatus::Cancelled, 'total' => 900]);

    expect(Client::spentAtLeast(500)->pluck('user_id')->all())->toBe([$big->id])
        ->and((float) Client::withTotalSpent()->where('user_id', $small->id)->first()->total_spent)->toBe(100.0);
});
