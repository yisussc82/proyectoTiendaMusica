<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('name_at_sale', 'price_at_sale', 'quantity')
            ->withTimestamps();
    }

    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('notify_on_discount')
            ->withTimestamps();
    }

    protected function finalPrice(): Attribute
    {
        return Attribute::get(fn () => $this->discount_price ?? $this->price);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeInStock(Builder $query): void
    {
        $query->where('stock', '>', 0);
    }

    public function scopeOnSale(Builder $query): void
    {
        $query->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price');
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhereHas('category', fn (Builder $category) => $category->where('name', 'like', "%{$term}%"));
        });
    }

    public function scopeInCategoryTree(Builder $query, Category $category): void
    {
        $categoryIds = $category->children()->pluck('id')->push($category->id);

        $query->whereIn('category_id', $categoryIds);
    }

    public function scopeBestSelling(Builder $query): void
    {
        $notCancelled = fn ($order) => $order->where('status', '!=', OrderStatus::Cancelled);

        $query->whereHas('orderItems.order', $notCancelled)
            ->withSum(['orderItems as units_sold' => fn ($items) => $items->whereHas('order', $notCancelled)], 'quantity')
            ->orderByDesc('units_sold');
    }
}
