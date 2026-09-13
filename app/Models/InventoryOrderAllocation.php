<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'catalog_product_id', 'quantity'])]
class InventoryOrderAllocation extends Model
{
    protected function casts(): array { return ['quantity' => 'integer']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'catalog_product_id'); }
}
