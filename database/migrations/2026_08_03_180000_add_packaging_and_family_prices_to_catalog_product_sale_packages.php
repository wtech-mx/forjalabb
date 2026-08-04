<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('catalog_product_sale_packages', function (Blueprint $table) {
            $table->decimal('packaging_cost', 10, 2)->default(0)->after('unit_cost');
            $table->decimal('family_multiplier', 5, 2)->default(1.50)->after('public_multiplier');
            $table->decimal('unit_family_price', 10, 2)->default(0)->after('total_cost');
            $table->decimal('family_price', 10, 2)->default(0)->after('unit_family_price');
            $table->decimal('family_profit', 10, 2)->default(0)->after('family_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_product_sale_packages', function (Blueprint $table) {
            $table->dropColumn([
                'packaging_cost',
                'family_multiplier',
                'unit_family_price',
                'family_price',
                'family_profit',
            ]);
        });
    }
};
