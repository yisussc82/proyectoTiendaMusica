<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        // Un usuario de cada rol con email conocido (contraseña: "password")
        foreach (Role::ALL as $role) {
            $user = User::factory()->create([
                'name' => ucfirst($role),
                'email' => "{$role}@musicstore.test",
            ]);
            $user->roles()->attach($roles[$role]);
        }

        // El usuario "customer" también tiene ficha de cliente
        $this->makeClient(User::where('email', 'customer@musicstore.test')->first());

        // 20 clientes más
        User::factory(20)->create()->each(function (User $user) use ($roles) {
            $user->roles()->attach($roles[Role::CUSTOMER]);
            $this->makeClient($user);
        });
    }

    /** Ficha de cliente con 2 direcciones, carrito y lista de deseos. */
    private function makeClient(User $user): void
    {
        $client = Client::factory()
            ->for($user)
            ->has(ClientAddress::factory(2)->sequence(['is_default' => true], ['is_default' => false]), 'addresses')
            ->create();

        $user->update(['name' => $client->full_name]); // mismo nombre en el usuario y en su ficha

        $productIds = Product::pluck('id')->all();

        // Carrito: 0-3 productos con cantidad (columna extra de la pivote)
        foreach (fake()->randomElements($productIds, fake()->numberBetween(0, 3)) as $id) {
            $user->cart()->attach($id, ['quantity' => fake()->numberBetween(1, 2)]);
        }

        // Lista de deseos: 0-4 productos con aviso de descuento (columna extra de la pivote)
        foreach (fake()->randomElements($productIds, fake()->numberBetween(0, 4)) as $id) {
            $user->wishlist()->attach($id, ['notify_on_discount' => fake()->boolean()]);
        }
    }
}
