<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'first_name', 'last_name', 'phone'])]
class Client extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(ClientAddress::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(ClientAddress::class)->where('is_default', true);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => "{$this->first_name} {$this->last_name}");
    }

    public function scopeWithTotalSpent(Builder $query): void
    {
        $query->withSum(['orders as total_spent' => fn ($orders) => $orders->where('status', '!=', OrderStatus::Cancelled)], 'total');
    }

    public function scopeSpentAtLeast(Builder $query, float $amount): void
    {
        $query->whereIn('user_id', Order::query()
            ->select('user_id')
            ->where('status', '!=', OrderStatus::Cancelled)
            ->groupBy('user_id')
            ->havingRaw('SUM(total) >= CAST(? AS DECIMAL(10, 2))', [$amount]));
    }
}
