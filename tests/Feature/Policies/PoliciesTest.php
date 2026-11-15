<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;

describe('ProductPolicy', function () {
    it('cualquiera ve un producto activo, pero uno inactivo solo administración y catálogo', function () {
        $active = Product::factory()->create();
        $inactive = Product::factory()->inactive()->create();

        expect(Gate::forUser(null)->allows('view', $active))->toBeTrue()
            ->and(Gate::forUser(null)->allows('view', $inactive))->toBeFalse()
            ->and(Gate::forUser(customer())->allows('view', $inactive))->toBeFalse()
            ->and(Gate::forUser(userWithRole(Role::CATALOG))->allows('view', $inactive))->toBeTrue();
    });

    it('solo administración y catálogo gestionan productos', function () {
        $product = Product::factory()->create();

        expect(Gate::forUser(userWithRole(Role::ADMIN))->allows('create', Product::class))->toBeTrue()
            ->and(Gate::forUser(userWithRole(Role::CATALOG))->allows('update', $product))->toBeTrue()
            ->and(Gate::forUser(userWithRole(Role::WAREHOUSE))->allows('update', $product))->toBeFalse()
            ->and(Gate::forUser(customer())->allows('delete', $product))->toBeFalse();
    });

    it('solo el administrador borra definitivamente, y solo si el producto nunca se ha vendido', function () {
        $neverSold = Product::factory()->create();
        $sold = Product::factory()->create();
        Order::factory()->create()->addProduct($sold);
        $admin = userWithRole(Role::ADMIN);

        expect(Gate::forUser($admin)->allows('forceDelete', $neverSold))->toBeTrue()
            ->and(Gate::forUser($admin)->allows('forceDelete', $sold))->toBeFalse()
            ->and(Gate::forUser(userWithRole(Role::CATALOG))->allows('forceDelete', $neverSold))->toBeFalse();
    });
});

describe('CategoryPolicy', function () {
    it('catálogo gestiona categorías y almacén no', function () {
        $category = Category::factory()->create();

        expect(Gate::forUser(userWithRole(Role::CATALOG))->allows('update', $category))->toBeTrue()
            ->and(Gate::forUser(userWithRole(Role::WAREHOUSE))->allows('update', $category))->toBeFalse();
    });

    it('solo se borra una categoría sin productos ni subcategorías', function () {
        $catalog = Gate::forUser(userWithRole(Role::CATALOG));
        $empty = Category::factory()->create();
        $withProducts = Category::factory()->has(Product::factory())->create();
        $withChildren = Category::factory()->create();
        Category::factory()->create(['parent_id' => $withChildren->id]);

        expect($catalog->allows('delete', $empty))->toBeTrue()
            ->and($catalog->allows('delete', $withProducts))->toBeFalse()
            ->and($catalog->allows('delete', $withChildren))->toBeFalse();
    });
});

describe('ClientPolicy', function () {
    it('administración y atención al cliente ven el listado de clientes', function () {
        expect(Gate::forUser(userWithRole(Role::ADMIN))->allows('viewAny', Client::class))->toBeTrue()
            ->and(Gate::forUser(userWithRole(Role::SUPPORT))->allows('viewAny', Client::class))->toBeTrue()
            ->and(Gate::forUser(userWithRole(Role::CATALOG))->allows('viewAny', Client::class))->toBeFalse()
            ->and(Gate::forUser(customer())->allows('viewAny', Client::class))->toBeFalse();
    });

    it('un cliente ve y edita su ficha, pero no la de otro', function () {
        $user = customer();
        $other = Client::factory()->create();
        $gate = Gate::forUser($user);

        expect($gate->allows('view', $user->client))->toBeTrue()
            ->and($gate->allows('update', $user->client))->toBeTrue()
            ->and($gate->allows('view', $other))->toBeFalse()
            ->and($gate->allows('update', $other))->toBeFalse();
    });

    it('solo el administrador puede borrar clientes', function () {
        $client = Client::factory()->create();

        expect(Gate::forUser(userWithRole(Role::ADMIN))->allows('delete', $client))->toBeTrue()
            ->and(Gate::forUser(userWithRole(Role::SUPPORT))->allows('delete', $client))->toBeFalse();
    });
});

describe('ClientAddressPolicy', function () {
    it('un cliente gestiona sus direcciones pero no las de otros ni borra la predeterminada', function () {
        $user = customer();
        $own = ClientAddress::factory()->for($user->client)->create();
        $default = ClientAddress::factory()->for($user->client)->default()->create();
        $other = ClientAddress::factory()->create();
        $gate = Gate::forUser($user);

        expect($gate->allows('update', $own))->toBeTrue()
            ->and($gate->allows('delete', $own))->toBeTrue()
            ->and($gate->allows('delete', $default))->toBeFalse()
            ->and($gate->allows('update', $other))->toBeFalse();
    });

    it('el personal sin ficha de cliente no puede crear direcciones', function () {
        expect(Gate::forUser(userWithRole(Role::ADMIN))->allows('create', ClientAddress::class))->toBeFalse();
    });
});

describe('OrderPolicy', function () {
    it('el cliente ve sus pedidos y el personal autorizado ve todos', function () {
        $user = customer();
        $own = Order::factory()->for($user)->create();
        $other = Order::factory()->create();

        expect(Gate::forUser($user)->allows('view', $own))->toBeTrue()
            ->and(Gate::forUser($user)->allows('view', $other))->toBeFalse()
            ->and(Gate::forUser(userWithRole(Role::WAREHOUSE))->allows('view', $other))->toBeTrue()
            ->and(Gate::forUser(userWithRole(Role::CATALOG))->allows('view', $other))->toBeFalse();
    });

    it('cancelar depende del rol y del estado del pedido', function () {
        $user = customer();
        $confirmed = Order::factory()->for($user)->create(['status' => OrderStatus::Confirmed]);
        $paid = Order::factory()->for($user)->create(['status' => OrderStatus::Paid]);
        $shipped = Order::factory()->for($user)->create(['status' => OrderStatus::Shipped]);
        $support = Gate::forUser(userWithRole(Role::SUPPORT));

        expect(Gate::forUser($user)->allows('cancel', $confirmed))->toBeTrue()
            ->and(Gate::forUser($user)->allows('cancel', $paid))->toBeFalse()
            ->and($support->allows('cancel', $paid))->toBeTrue()
            ->and($support->allows('cancel', $shipped))->toBeFalse();
    });
});
