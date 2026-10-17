<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional()->numerify('Piso #, Puerta #'),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->numerify('#####'),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }
}
