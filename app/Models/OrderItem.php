<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['item_type', 'catalog_product_id', 'catalog_bundle_id', 'catalog_product_sale_package_id', 'product_name', 'contents_snapshot', 'sale_package_name', 'sale_package_quantity', 'selected_colors', 'unit_price', 'quantity', 'line_total'])]
class OrderItem extends Model
{
    protected function casts(): array { return ['sale_package_quantity' => 'integer', 'selected_colors' => 'array', 'unit_price' => 'decimal:2', 'line_total' => 'decimal:2']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'catalog_product_id'); }
    public function bundle(): BelongsTo { return $this->belongsTo(CatalogBundle::class, 'catalog_bundle_id'); }
    public function salePackage(): BelongsTo { return $this->belongsTo(CatalogProductSalePackage::class, 'catalog_product_sale_package_id'); }
}
