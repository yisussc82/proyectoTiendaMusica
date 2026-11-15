<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshFeaturedProducts extends Command
{
    protected $signature = 'catalog:refresh-featured {--limit=8 : Número de productos destacados}';

    protected $description = 'Marca como destacados los productos más vendidos';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        if ($limit < 1) {
            $this->error('El límite debe ser un número mayor que 0.');

            return self::FAILURE;
        }

        $bestSellerIds = Product::active()->bestSelling()->limit($limit)->get()->pluck('id');

        if ($bestSellerIds->isEmpty()) {
            $this->warn('Todavía no hay ventas: se mantienen los destacados actuales.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($bestSellerIds) {
            Product::where('is_featured', true)->update(['is_featured' => false]);
            Product::whereIn('id', $bestSellerIds)->update(['is_featured' => true]);
        });

        $this->info("Productos destacados: {$bestSellerIds->count()}");

        return self::SUCCESS;
    }
}
