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
    'cost_subtotal',
    'friends_price',
    'public_price',
    'friends_profit',
    'public_profit',
    'stock',
    'cover_photo_path',
    'presentation_mode',
    'badge',
    'image_path',
    'icon',
    'url',
    'action_label',
    'presentation',
    'is_active',
    'is_featured',
    'sort_order',
])]
class CatalogProduct extends Model
{
    use HasFactory;

    public const PRESENTATION_PHOTO = 'photo';
    public const PRESENTATION_ICON = 'icon';
    public const PRESENTATION_TEQUILA = 'tequila_set';
    public const PRESENTATION_PACKAGE = 'package';

    public const PRESENTATIONS = [
        self::PRESENTATION_PHOTO => 'Foto',
        self::PRESENTATION_ICON => 'Icono',
        self::PRESENTATION_TEQUILA => 'Set tequileros',
        self::PRESENTATION_PACKAGE => 'Paquete destacado',
    ];

    public const MODE_GALLERY = 'gallery';
    public const MODE_CUSTOMIZATION = 'customization';

    public const PRESENTATION_MODES = [
        self::MODE_GALLERY => 'Galeria de fotos',
        self::MODE_CUSTOMIZATION => 'Personalizacion de producto',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'cost_subtotal' => 'decimal:2',
            'friends_price' => 'decimal:2',
            'public_price' => 'decimal:2',
            'friends_profit' => 'decimal:2',
            'public_profit' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function costs(): HasMany
    {
        return $this->hasMany(CatalogProductCost::class)->orderBy('sort_order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(CatalogProductOption::class)->orderBy('sort_order');
    }

    public function salePackages(): HasMany
    {
        return $this->hasMany(CatalogProductSalePackage::class)->orderBy('sort_order');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getResolvedUrlAttribute(): string
    {
        return $this->url ?: url('/catalogo/'.$this->slug);
    }

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->cover_photo_path ?: $this->image_path;

        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset($path);
    }
}
