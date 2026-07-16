<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smart_tags', function (Blueprint $table) {
            $table->string('owner_email')->nullable()->after('owner_phone');
            $table->string('secondary_contact_email')->nullable()->after('secondary_contact_phone');
            $table->string('vet_email')->nullable()->after('vet_phone');
        });
    }

    public function down(): void
    {
        Schema::table('smart_tags', function (Blueprint $table) {
            $table->dropColumn([
                'owner_email',
                'secondary_contact_email',
                'vet_email',
            ]);
        });
    }
};
