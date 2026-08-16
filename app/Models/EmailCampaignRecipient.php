<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'tracking_token', 'status', 'error_message', 'sent_at', 'opened_at', 'open_count', 'clicked_at', 'click_count', 'last_clicked_url'])]
class EmailCampaignRecipient extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['sent_at' => 'datetime', 'opened_at' => 'datetime', 'clicked_at' => 'datetime'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'email_campaign_id');
    }
}
