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
use Illuminate\Support\Facades\DB;
use LogicException;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('name_at_sale', 'price_at_sale', 'quantity')
            ->withTimestamps()
            ->withTrashed();
    }

    public function addProduct(Product $product, int $quantity = 1): void
    {
        $this->products()->attach($product->id, [
            'name_at_sale' => $product->name,
            'price_at_sale' => $product->final_price,
            'quantity' => $quantity,
        ]);
    }

    public function recalculateTotal(): void
    {
        $itemsTotal = $this->items()->get()->sum(fn (OrderItem $item) => $item->subtotal());

        $this->update(['total' => round($itemsTotal + $this->shipping_cost, 2)]);
    }

    public function confirm(): void
    {
        DB::transaction(function () {
            $order = self::lockForUpdate()->findOrFail($this->id);

            if ($order->status !== OrderStatus::Pending) {
                throw new LogicException('Solo se puede confirmar un pedido pendiente.');
            }

            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    throw new LogicException("No hay stock suficiente de {$product->name}.");
                }

                $product->decrement('stock', $item->quantity);
            }

            $order->update(['status' => OrderStatus::Confirmed]);
        });

        $this->refresh();
    }

    public function cancel(): void
    {
        DB::transaction(function () {
            $order = self::lockForUpdate()->findOrFail($this->id);

            if ($order->status === OrderStatus::Cancelled) {
                return;
            }

            if (in_array($order->status, [OrderStatus::Shipped, OrderStatus::Delivered])) {
                throw new LogicException('No se puede cancelar un pedido ya enviado.');
            }

            if ($order->status !== OrderStatus::Pending) {
                foreach ($order->items as $item) {
                    Product::withTrashed()->whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => OrderStatus::Cancelled]);
        });

        $this->refresh();
    }
}
