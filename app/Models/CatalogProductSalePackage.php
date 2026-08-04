<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'quantity',
    'public_multiplier',
    'family_multiplier',
    'unit_cost',
    'packaging_cost',
    'total_cost',
    'unit_family_price',
    'family_price',
    'family_profit',
    'unit_public_price',
    'public_price',
    'public_profit',
    'is_default',
    'sort_order',
])]
class CatalogProductSalePackage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'public_multiplier' => 'decimal:2',
            'family_multiplier' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'packaging_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'unit_family_price' => 'decimal:2',
            'family_price' => 'decimal:2',
            'family_profit' => 'decimal:2',
            'unit_public_price' => 'decimal:2',
            'public_price' => 'decimal:2',
            'public_profit' => 'decimal:2',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CatalogProduct::class, 'catalog_product_id');
    }
}
