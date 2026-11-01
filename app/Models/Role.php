<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Role extends Model
{
    public const ADMIN = 'admin';
    public const CATALOG = 'catalog';
    public const WAREHOUSE = 'warehouse';
    public const SUPPORT = 'support';
    public const CUSTOMER = 'customer';

    public const ALL = [self::ADMIN, self::CATALOG, self::WAREHOUSE, self::SUPPORT, self::CUSTOMER];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
