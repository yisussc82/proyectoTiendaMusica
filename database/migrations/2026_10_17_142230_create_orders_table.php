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

            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('order_number', 20)->unique();
            $table->string('status', 20)->default('pending');

            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->decimal('total', 10, 2);

            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city', 100);
            $table->string('shipping_province', 100);
            $table->string('shipping_postal_code', 10);

            $table->string('payment_method', 20)->nullable();
            $table->string('payment_reference')->nullable();
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
