<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->time('delivery_time')->nullable()->after('delivery_at');
            $table->text('delivery_place')->nullable()->after('delivery_time');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('catalog_product_sale_package_id')->nullable()->after('catalog_bundle_id')->constrained()->nullOnDelete();
            $table->string('sale_package_name')->nullable()->after('contents_snapshot');
            $table->unsignedInteger('sale_package_quantity')->nullable()->after('sale_package_name');
        });

        Schema::create('order_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('path')->nullable();
            $table->text('url')->nullable();
            $table->string('label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_references');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('catalog_product_sale_package_id');
            $table->dropColumn(['sale_package_name', 'sale_package_quantity']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_time', 'delivery_place']);
        });
    }
};
