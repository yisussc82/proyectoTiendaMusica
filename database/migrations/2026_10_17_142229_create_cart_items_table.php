<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Carrito: pivote n:n entre usuarios y productos con la cantidad como columna extra.
     * El precio no se guarda aquí: se usa el precio actual y se congela al crear el pedido.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'product_id']); // un producto una sola vez por carrito
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
