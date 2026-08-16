<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_campaign_recipients', function (Blueprint $table) {
            $table->string('tracking_token', 64)->nullable()->unique()->after('email');
            $table->timestamp('opened_at')->nullable()->after('sent_at');
            $table->unsignedInteger('open_count')->default(0)->after('opened_at');
            $table->timestamp('clicked_at')->nullable()->after('open_count');
            $table->unsignedInteger('click_count')->default(0)->after('clicked_at');
            $table->text('last_clicked_url')->nullable()->after('click_count');
        });
    }

    public function down(): void
    {
        Schema::table('email_campaign_recipients', function (Blueprint $table) {
            $table->dropUnique(['tracking_token']);
            $table->dropColumn(['tracking_token', 'opened_at', 'open_count', 'clicked_at', 'click_count', 'last_clicked_url']);
        });
    }
};
