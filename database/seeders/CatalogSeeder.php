<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    private const CATALOG = [
        'Guitarras' => ['Eléctricas' => 'Guitarra eléctrica', 'Acústicas' => 'Guitarra acústica', 'Clásicas' => 'Guitarra clásica'],
        'Bajos' => ['Bajos eléctricos' => 'Bajo eléctrico', 'Bajos acústicos' => 'Bajo acústico'],
        'Teclados' => ['Pianos digitales' => 'Piano digital', 'Sintetizadores' => 'Sintetizador'],
        'Batería y percusión' => ['Baterías acústicas' => 'Batería acústica', 'Baterías electrónicas' => 'Batería electrónica'],
        'Audio y estudio' => ['Micrófonos' => 'Micrófono', 'Interfaces de audio' => 'Interfaz de audio', 'Auriculares' => 'Auriculares'],
    ];

    public function run(): void
    {
        foreach (self::CATALOG as $parentName => $children) {
            $parent = Category::create(['name' => $parentName, 'slug' => Str::slug($parentName)]);

            foreach ($children as $childName => $productType) {
                $child = $parent->children()->create(['name' => $childName, 'slug' => Str::slug($childName)]);

                Product::factory(6)
                    ->for($child)
                    ->state(fn (array $a) => ['name' => "{$productType} {$a['name']}"])
                    ->has(
                        ProductImage::factory(3)->sequence(
                            fn (Sequence $s) => ['is_main' => $s->index % 3 === 0, 'sort_order' => $s->index % 3]
                        ),
                        'images'
                    )
                    ->create();
            }
        }
    }
}
