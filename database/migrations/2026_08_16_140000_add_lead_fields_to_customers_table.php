<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('company')->nullable()->after('name');
            $table->string('whatsapp', 30)->nullable()->after('phone');
            $table->string('interested_service')->nullable()->after('address');
            $table->string('lead_source', 50)->nullable()->after('interested_service')->index();
            $table->string('lead_status', 30)->default('customer')->after('lead_source')->index();
            $table->timestamp('contacted_at')->nullable()->after('lead_status');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['lead_source']);
            $table->dropIndex(['lead_status']);
            $table->dropColumn(['company', 'whatsapp', 'interested_service', 'lead_source', 'lead_status', 'contacted_at']);
        });
    }
};
