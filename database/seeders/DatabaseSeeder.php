<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        fake()->seed(2026);

        $this->call([
            RoleSeeder::class,
            CatalogSeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
