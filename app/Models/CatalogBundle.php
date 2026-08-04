<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'description',
    'items_cost',
    'packaging_cost',
    'total_cost',
    'family_multiplier',
    'family_price',
    'family_profit',
    'public_multiplier',
    'public_price',
    'public_profit',
    'cover_photo_path',
    'is_active',
    'is_featured',
    'sort_order',
])]
class CatalogBundle extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'items_cost' => 'decimal:2',
            'packaging_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'family_multiplier' => 'decimal:2',
            'family_price' => 'decimal:2',
            'family_profit' => 'decimal:2',
            'public_multiplier' => 'decimal:2',
            'public_price' => 'decimal:2',
            'public_profit' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(CatalogBundleItem::class)->orderBy('sort_order');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CatalogBundlePhoto::class)->orderBy('sort_order');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->cover_photo_path) {
            return null;
        }

        return Str::startsWith($this->cover_photo_path, ['http://', 'https://'])
            ? $this->cover_photo_path
            : asset($this->cover_photo_path);
    }
}
