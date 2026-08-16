<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'company', 'phone', 'whatsapp', 'email', 'address', 'interested_service', 'lead_source', 'lead_status', 'contacted_at', 'notes'])]
class Customer extends Model
{
    use HasFactory;

    public const LEAD_STATUSES = [
        'pending' => 'Pendiente',
        'contacted' => 'Contactado',
        'converted' => 'Convertido en cliente',
        'discarded' => 'Descartado',
        'customer' => 'Cliente',
    ];

    protected function casts(): array
    {
        return ['contacted_at' => 'datetime'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
