<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // No se puede borrar (físicamente) un usuario que tenga pedidos.
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('order_number', 20)->unique(); // ej. MS-2026-000123
            $table->string('status', 20)->default('pending'); // pending, paid, shipped, delivered, cancelled

            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Copia de la dirección en el momento de la compra
            // (si el cliente cambia su dirección, el pedido no debe cambiar).
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city', 100);
            $table->string('shipping_province', 100);
            $table->string('shipping_postal_code', 10);

            // Pago (PayPal u otro)
            $table->string('payment_method', 20)->nullable();
            $table->string('payment_reference')->nullable(); // id de la transacción
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
