<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Role extends Model
{
    public const ADMIN = 'admin';         // control total
    public const MANAGER = 'manager';     // catálogo, pedidos e informes
    public const WAREHOUSE = 'warehouse'; // stock y envíos
    public const SUPPORT = 'support';     // atención al cliente
    public const CUSTOMER = 'customer';   // compra en la tienda

    public const ALL = [self::ADMIN, self::MANAGER, self::WAREHOUSE, self::SUPPORT, self::CUSTOMER];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
