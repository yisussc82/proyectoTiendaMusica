<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class LowStockProducts extends Command
{
    protected $signature = 'products:low-stock {--threshold=5 : Stock máximo para considerarlo bajo}';

    protected $description = 'Lista los productos activos con poco stock';

    public function handle(): int
    {
        $threshold = $this->option('threshold');

        if (! ctype_digit((string) $threshold)) {
            $this->error('El umbral debe ser un número entero igual o mayor que 0.');

            return self::FAILURE;
        }

        $products = Product::active()
            ->where('stock', '<=', (int) $threshold)
            ->orderBy('stock')
            ->get();

        if ($products->isEmpty()) {
            $this->info('No hay productos con poco stock.');

            return self::SUCCESS;
        }

        $this->table(
            ['SKU', 'Producto', 'Stock'],
            $products->map(fn (Product $product) => [$product->sku, $product->name, $product->stock]),
        );

        return self::SUCCESS;
    }
}
