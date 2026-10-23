<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Jetstream: tokens de la API (Sanctum), foto de perfil y doble factor
    use HasApiTokens, HasProfilePhoto, TwoFactorAuthenticatable;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---------- Relaciones ----------

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Carrito: n:n con productos y columna extra "quantity". */
    public function cart(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cart_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /** Lista de deseos: n:n con productos y columna extra "notify_on_discount". */
    public function wishlist(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('notify_on_discount')
            ->withTimestamps();
    }

    // ---------- Helpers ----------

    /** Ej.: $user->hasRole('admin') o $user->hasRole('admin', 'catalog') */
    public function hasRole(string ...$roles): bool
    {
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }
}
