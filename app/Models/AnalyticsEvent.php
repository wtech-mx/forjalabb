<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['event_type', 'path', 'label', 'session_id', 'referrer', 'device', 'utm_source', 'utm_medium', 'utm_campaign', 'occurred_at'])]
class AnalyticsEvent extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }
}
