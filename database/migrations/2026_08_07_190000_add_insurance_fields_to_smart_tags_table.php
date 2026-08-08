<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smart_tags', function (Blueprint $table): void {
            $table->boolean('has_vehicle_insurance')->default(false)->after('motorcycle_plate');
            $table->string('vehicle_insurance_policy', 120)->nullable()->after('has_vehicle_insurance');
            $table->date('vehicle_insurance_expires_at')->nullable()->after('vehicle_insurance_policy');
            $table->boolean('has_public_health_insurance')->default(false)->after('vehicle_insurance_expires_at');
            $table->string('public_health_provider', 80)->nullable()->after('has_public_health_insurance');
            $table->string('public_health_number', 120)->nullable()->after('public_health_provider');
        });
    }

    public function down(): void
    {
        Schema::table('smart_tags', function (Blueprint $table): void {
            $table->dropColumn([
                'has_vehicle_insurance',
                'vehicle_insurance_policy',
                'vehicle_insurance_expires_at',
                'has_public_health_insurance',
                'public_health_provider',
                'public_health_number',
            ]);
        });
    }
};
