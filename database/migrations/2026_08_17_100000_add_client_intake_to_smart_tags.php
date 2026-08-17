<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smart_tags', function (Blueprint $table) {
            $table->string('intake_token', 64)->nullable()->unique()->after('token');
            $table->text('payment_code')->nullable()->after('intake_token');
            $table->string('intake_status', 30)->default('active')->after('payment_code')->index();
            $table->timestamp('client_submitted_at')->nullable()->after('intake_status');
        });
    }

    public function down(): void
    {
        Schema::table('smart_tags', function (Blueprint $table) {
            $table->dropUnique(['intake_token']);
            $table->dropColumn(['intake_token', 'payment_code', 'intake_status', 'client_submitted_at']);
        });
    }
};
