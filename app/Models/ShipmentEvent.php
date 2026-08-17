<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['phase','title','description','occurred_at','is_public'])]
class ShipmentEvent extends Model { use HasFactory; protected function casts():array{return ['occurred_at'=>'datetime','is_public'=>'boolean'];} public function shipment():BelongsTo{return $this->belongsTo(Shipment::class);} public function media():HasMany{return $this->hasMany(ShipmentMedia::class);} }
