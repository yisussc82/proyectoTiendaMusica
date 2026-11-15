<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['parent_id', 'name', 'slug'])]
class Category extends Model
{
    use HasFactory, SoftDeletes;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function treeOptions(): array
    {
        return self::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get()
            ->flatMap(fn (Category $parent) => [$parent->slug => $parent->name]
                + $parent->children->mapWithKeys(fn (Category $child) => [$child->slug => "— {$child->name}"])->all())
            ->all();
    }
}
