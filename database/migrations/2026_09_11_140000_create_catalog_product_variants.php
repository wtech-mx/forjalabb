<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL can leave partial DDL behind when a migration fails midway. Clean only
        // these new structures while this migration is still pending, then rebuild them.
        Schema::dropIfExists('inventory_variant_order_allocations');
        if (Schema::hasColumn('inventory_movements', 'catalog_product_variant_id')) {
            Schema::table('inventory_movements', fn (Blueprint $table) => $table->dropConstrainedForeignId('catalog_product_variant_id'));
        }
        if (Schema::hasColumn('order_items', 'selected_variant_ids')) {
            Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('selected_variant_ids'));
        }
        Schema::dropIfExists('catalog_product_variants');

        Schema::create('catalog_product_variants', function (Blueprint $table) {
            $table->id(); $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique(); $table->string('color')->nullable(); $table->string('size')->nullable();
            $table->unsignedInteger('stock')->default(0); $table->unsignedInteger('minimum_stock')->default(2); $table->boolean('is_active')->default(true); $table->timestamps();
            $table->unique(['catalog_product_id', 'color', 'size'], 'product_variant_attributes_unique');
        });
        Schema::table('order_items', fn (Blueprint $table) => $table->json('selected_variant_ids')->nullable()->after('selected_colors'));
        Schema::table('inventory_movements', fn (Blueprint $table) => $table->foreignId('catalog_product_variant_id')->nullable()->after('catalog_product_id')->constrained()->nullOnDelete());
        Schema::create('inventory_variant_order_allocations', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete(); $table->unsignedBigInteger('catalog_product_variant_id'); $table->foreign('catalog_product_variant_id', 'variant_allocation_variant_fk')->references('id')->on('catalog_product_variants')->cascadeOnDelete(); $table->unsignedInteger('quantity'); $table->timestamps();
            $table->unique(['order_id', 'catalog_product_variant_id'], 'order_variant_allocation_unique');
        });

        $usedSkus = [];
        DB::table('catalog_product_options')->where('group', 'color')->orderBy('id')->get()->each(function ($option) use (&$usedSkus) {
            $product = DB::table('catalog_products')->find($option->catalog_product_id); if (! $product) return;
            $base = strtoupper(Str::slug($product->name.'-'.$option->name, '-')); $sku = substr($base, 0, 52); $suffix = 1;
            while (isset($usedSkus[$sku]) || DB::table('catalog_product_variants')->where('sku', $sku)->exists()) $sku = substr($base, 0, 47).'-'.++$suffix;
            $usedSkus[$sku] = true;
            DB::table('catalog_product_variants')->insert(['catalog_product_id'=>$product->id,'sku'=>$sku,'color'=>$option->name,'size'=>null,'stock'=>$option->stock,'minimum_stock'=>2,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventory_variant_order_allocations');
        Schema::table('inventory_movements', fn (Blueprint $table) => $table->dropConstrainedForeignId('catalog_product_variant_id'));
        Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('selected_variant_ids'));
        Schema::dropIfExists('catalog_product_variants');
    }
};
