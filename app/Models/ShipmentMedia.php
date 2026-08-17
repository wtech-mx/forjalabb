<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Support\Str;
#[Fillable(['media_type','file_path','original_name'])]
class ShipmentMedia extends Model { use HasFactory; public function event():BelongsTo{return $this->belongsTo(ShipmentEvent::class,'shipment_event_id');} public function getUrlAttribute():string{return Str::startsWith($this->file_path,['http://','https://'])?$this->file_path:asset($this->file_path);} }
