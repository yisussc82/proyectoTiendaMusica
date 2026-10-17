<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id', 'order_number', 'status', 'shipping_cost', 'total',
    'shipping_address_line_1', 'shipping_address_line_2',
    'shipping_city', 'shipping_province', 'shipping_postal_code',
    'payment_method', 'payment_reference', 'paid_at',
])]
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // ---------- Relaciones ----------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** n:n con productos a través de order_items (con columnas extra en la pivote). */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('name_at_sale', 'price_at_sale', 'quantity')
            ->withTimestamps()
            ->withTrashed(); // un pedido antiguo sigue mostrando productos borrados
    }

    // ---------- Lógica ----------

    /** Añade un producto al pedido copiando su nombre y precio actuales. */
    public function addProduct(Product $product, int $quantity = 1): void
    {
        $this->products()->attach($product->id, [
            'name_at_sale' => $product->name,
            'price_at_sale' => $product->final_price,
            'quantity' => $quantity,
        ]);
    }

    /** Recalcula el total a partir de las líneas del pedido + gastos de envío. */
    public function recalculateTotal(): void
    {
        $itemsTotal = $this->items()->get()->sum(fn (OrderItem $item) => $item->subtotal());

        $this->update(['total' => round($itemsTotal + $this->shipping_cost, 2)]);
    }
}
