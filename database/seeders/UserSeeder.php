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

        foreach (Role::ALL as $role) {
            $user = User::factory()->create([
                'name' => ucfirst($role),
                'email' => "{$role}@musicstore.test",
            ]);
            $user->roles()->attach($roles[$role]);
        }

        $this->makeClient(User::where('email', 'customer@musicstore.test')->first());

        User::factory(20)->create()->each(function (User $user) use ($roles) {
            $user->roles()->attach($roles[Role::CUSTOMER]);
            $this->makeClient($user);
        });
    }

    private function makeClient(User $user): void
    {
        $client = Client::factory()
            ->for($user)
            ->has(ClientAddress::factory(2)->sequence(['is_default' => true], ['is_default' => false]), 'addresses')
            ->create();

        $user->update(['name' => $client->full_name]);

        $productIds = Product::pluck('id')->all();

        foreach (fake()->randomElements($productIds, fake()->numberBetween(0, 3)) as $id) {
            $user->cart()->attach($id, ['quantity' => fake()->numberBetween(1, 2)]);
        }

        foreach (fake()->randomElements($productIds, fake()->numberBetween(0, 4)) as $id) {
            $user->wishlist()->attach($id, ['notify_on_discount' => fake()->boolean()]);
        }
    }
}
