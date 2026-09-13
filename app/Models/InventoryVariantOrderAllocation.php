<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
#[Fillable(['order_id','catalog_product_variant_id','quantity'])]
class InventoryVariantOrderAllocation extends Model { protected function casts():array{return ['quantity'=>'integer'];} public function order():BelongsTo{return $this->belongsTo(Order::class);} public function variant():BelongsTo{return $this->belongsTo(CatalogProductVariant::class,'catalog_product_variant_id');} }
