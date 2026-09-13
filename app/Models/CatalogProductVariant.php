<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['sku','color','size','stock','minimum_stock','is_active'])]
class CatalogProductVariant extends Model { protected function casts():array{return ['stock'=>'integer','minimum_stock'=>'integer','is_active'=>'boolean'];} public function product():BelongsTo{return $this->belongsTo(CatalogProduct::class,'catalog_product_id');} public function allocations():HasMany{return $this->hasMany(InventoryVariantOrderAllocation::class);} public function getLabelAttribute():string{return collect([$this->color,$this->size])->filter()->join(' · ');} }
