<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['catalog_product_id', 'catalog_product_variant_id', 'order_id', 'created_by', 'type', 'quantity', 'balance_after', 'note'])]
class InventoryMovement extends Model
{
    protected function casts(): array { return ['quantity' => 'integer', 'balance_after' => 'integer']; }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'catalog_product_id'); }
    public function variant(): BelongsTo { return $this->belongsTo(CatalogProductVariant::class, 'catalog_product_variant_id'); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
