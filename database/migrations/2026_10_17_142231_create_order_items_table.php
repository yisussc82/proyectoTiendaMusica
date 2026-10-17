<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Líneas de pedido: pivote n:n entre pedidos y productos con columnas extra.
     * Nombre y precio se copian en el momento de la venta para que el pedido
     * no cambie si después se modifica el producto.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('name_at_sale', 150);
            $table->decimal('price_at_sale', 10, 2);
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->unique(['order_id', 'product_id']); // un producto una sola vez por pedido
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
