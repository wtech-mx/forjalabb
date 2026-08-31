<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable(['folio', 'customer_id', 'created_by', 'ordered_at', 'delivery_at', 'delivery_time', 'delivery_method', 'delivery_place', 'delivery_map_url', 'delivery_lat', 'delivery_lng', 'status', 'discount_type', 'discount_value', 'subtotal', 'discount_amount', 'has_shipping', 'shipping_cost', 'total', 'advance_payment', 'balance_due', 'observations', 'archived_at'])]
class Order extends Model
{
    use HasFactory;

    public const DELIVERY_METHODS = [
        'skydropx' => 'Skydropx / foráneo',
        'cdmx' => 'Entrega CDMX',
        'pickup' => 'Recoger en tienda',
    ];

    public const STATUSES = [
        'pending' => 'Pendiente',
        'in_progress' => 'En producción',
        'ready' => 'Listo para entregar',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'date', 'delivery_at' => 'date', 'delivery_time' => 'datetime:H:i', 'archived_at' => 'datetime', 'has_shipping' => 'boolean',
            'delivery_lat' => 'decimal:7', 'delivery_lng' => 'decimal:7',
            'subtotal' => 'decimal:2', 'discount_value' => 'decimal:2', 'discount_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2', 'total' => 'decimal:2', 'advance_payment' => 'decimal:2', 'balance_due' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function references(): HasMany { return $this->hasMany(OrderReference::class)->orderBy('sort_order'); }
    public function shipment(): HasOne { return $this->hasOne(Shipment::class); }

    public function getDeliveryMapsLinkAttribute(): ?string
    {
        if ($this->delivery_lat && $this->delivery_lng) {
            return 'https://www.google.com/maps/search/?api=1&query='.$this->delivery_lat.','.$this->delivery_lng;
        }

        if (! $this->delivery_map_url) {
            return null;
        }

        return Str::contains($this->delivery_map_url, '/maps/embed')
            ? null
            : $this->delivery_map_url;
    }
}
