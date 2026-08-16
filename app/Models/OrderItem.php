<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['item_type', 'catalog_product_id', 'catalog_bundle_id', 'product_name', 'contents_snapshot', 'unit_price', 'quantity', 'line_total'])]
class OrderItem extends Model
{
    protected function casts(): array { return ['unit_price' => 'decimal:2', 'line_total' => 'decimal:2']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'catalog_product_id'); }
    public function bundle(): BelongsTo { return $this->belongsTo(CatalogBundle::class, 'catalog_bundle_id'); }
}
