<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['created_by', 'name', 'subject', 'preview_text', 'content_html', 'featured_type', 'featured_id', 'related_product_ids', 'status', 'recipient_count', 'sent_count', 'failed_count', 'sent_at'])]
class EmailCampaign extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['related_product_ids' => 'array', 'sent_at' => 'datetime'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class);
    }
}
