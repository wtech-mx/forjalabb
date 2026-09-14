<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_products', fn (Blueprint $table) => $table->integer('stock')->default(0)->change());
        Schema::table('catalog_product_variants', fn (Blueprint $table) => $table->integer('stock')->default(0)->change());
        Schema::table('inventory_movements', fn (Blueprint $table) => $table->integer('balance_after')->change());
    }

    public function down(): void
    {
        Schema::table('catalog_products', fn (Blueprint $table) => $table->unsignedInteger('stock')->default(0)->change());
        Schema::table('catalog_product_variants', fn (Blueprint $table) => $table->unsignedInteger('stock')->default(0)->change());
        Schema::table('inventory_movements', fn (Blueprint $table) => $table->unsignedInteger('balance_after')->change());
    }
};
