<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public const BRANDS = ['Fender', 'Gibson', 'Ibanez', 'Yamaha', 'Roland', 'Korg', 'Pearl', 'Shure', 'Focusrite', 'Sennheiser'];

    public function definition(): array
    {
        $brand = fake()->randomElement(self::BRANDS);

        return [
            'category_id' => Category::factory(),
            'brand' => $brand,
            'name' => $brand.' '.fake()->bothify('??-###'),
            // Las closures se evalúan al final, así usan el "name" definitivo aunque un state lo cambie
            'slug' => fn (array $a) => Str::slug($a['name']).'-'.fake()->unique()->numerify('####'),
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'description' => '<p>'.implode('</p><p>', fake()->paragraphs(2)).'</p>',
            'price' => fake()->randomFloat(2, 20, 2500),
            'discount_price' => fn (array $a) => fake()->boolean(20) ? round($a['price'] * 0.85, 2) : null,
            'stock' => fake()->numberBetween(0, 50),
            'is_active' => true,
            'is_featured' => fake()->boolean(15),
        ];
    }

    public function onSale(): static
    {
        return $this->state(fn (array $a) => ['discount_price' => round($a['price'] * 0.8, 2)]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
