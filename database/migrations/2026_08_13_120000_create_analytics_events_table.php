<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_type', 40)->index();
            $table->string('path', 500)->index();
            $table->string('label', 160)->nullable()->index();
            $table->string('session_id', 64)->index();
            $table->string('referrer', 500)->nullable();
            $table->string('device', 20)->nullable()->index();
            $table->string('utm_source', 120)->nullable()->index();
            $table->string('utm_medium', 120)->nullable();
            $table->string('utm_campaign', 160)->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
