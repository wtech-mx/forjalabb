<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_type', 20)->default('product')->after('order_id');
            $table->foreignId('catalog_bundle_id')->nullable()->after('catalog_product_id')->constrained()->nullOnDelete();
            $table->text('contents_snapshot')->nullable()->after('product_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('catalog_bundle_id');
            $table->dropColumn(['item_type', 'contents_snapshot']);
        });
    }
};
