<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Role extends Model
{
    public const ADMIN = 'admin';         // administración: control total
    public const CATALOG = 'catalog';     // catálogo: productos, categorías e imágenes
    public const WAREHOUSE = 'warehouse'; // almacén: stock, preparación y envío de pedidos
    public const SUPPORT = 'support';     // atención al cliente: consulta clientes y pedidos
    public const CUSTOMER = 'customer';   // cliente: compra en la tienda

    public const ALL = [self::ADMIN, self::CATALOG, self::WAREHOUSE, self::SUPPORT, self::CUSTOMER];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
