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
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->decimal('cost_subtotal', 10, 2)->default(0)->after('description');
            $table->decimal('friends_price', 10, 2)->default(0)->after('cost_subtotal');
            $table->decimal('public_price', 10, 2)->default(0)->after('friends_price');
            $table->decimal('friends_profit', 10, 2)->default(0)->after('public_price');
            $table->decimal('public_profit', 10, 2)->default(0)->after('friends_profit');
            $table->unsignedInteger('stock')->default(0)->after('public_profit');
            $table->string('cover_photo_path')->nullable()->after('stock');
            $table->string('presentation_mode')->default('gallery')->after('cover_photo_path');
        });

        Schema::create('catalog_product_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('cost', 10, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('catalog_product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->string('group')->default('tipo');
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_product_options');
        Schema::dropIfExists('catalog_product_costs');

        Schema::table('catalog_products', function (Blueprint $table) {
            $table->dropColumn([
                'cost_subtotal',
                'friends_price',
                'public_price',
                'friends_profit',
                'public_profit',
                'stock',
                'cover_photo_path',
                'presentation_mode',
            ]);
        });
    }
};
