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
        Schema::create('catalog_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('items_cost', 10, 2)->default(0);
            $table->decimal('packaging_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('family_multiplier', 5, 2)->default(1.50);
            $table->decimal('family_price', 10, 2)->default(0);
            $table->decimal('family_profit', 10, 2)->default(0);
            $table->decimal('public_multiplier', 5, 2)->default(1.80);
            $table->decimal('public_price', 10, 2)->default(0);
            $table->decimal('public_profit', 10, 2)->default(0);
            $table->string('cover_photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('catalog_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_bundle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('catalog_bundle_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_bundle_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_bundle_photos');
        Schema::dropIfExists('catalog_bundle_items');
        Schema::dropIfExists('catalog_bundles');
    }
};
