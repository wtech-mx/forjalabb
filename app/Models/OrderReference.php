<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['type', 'path', 'url', 'label', 'sort_order'])]
class OrderReference extends Model
{
    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getDisplayUrlAttribute(): ?string
    {
        if ($this->type === 'link') {
            return $this->url;
        }

        if (! $this->path) {
            return null;
        }

        return Str::startsWith($this->path, ['http://', 'https://'])
            ? $this->path
            : asset($this->path);
    }
}
