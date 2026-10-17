<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id', 'name', 'slug', 'brand', 'sku', 'description',
    'price', 'discount_price', 'stock', 'is_active', 'is_featured',
])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // ---------- Relaciones ----------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('name_at_sale', 'price_at_sale', 'quantity')
            ->withTimestamps();
    }

    /** Usuarios que tienen el producto en su lista de deseos. */
    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('notify_on_discount')
            ->withTimestamps();
    }

    // ---------- Atributos calculados ----------

    /** $product->final_price: precio con descuento si lo hay. */
    protected function finalPrice(): Attribute
    {
        return Attribute::get(fn () => $this->discount_price ?? $this->price);
    }
}
