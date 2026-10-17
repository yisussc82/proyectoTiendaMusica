<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'name' => ucfirst(fake()->unique()->word()),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
        ];
    }
}
