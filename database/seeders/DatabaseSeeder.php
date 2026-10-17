<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents; // al sembrar no se disparan eventos (ni emails, ni listeners)

    public function run(): void
    {
        // Semilla fija: cada "migrate:fresh --seed" genera exactamente los mismos datos
        fake()->seed(2026);

        $this->call([
            RoleSeeder::class,
            CatalogSeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
