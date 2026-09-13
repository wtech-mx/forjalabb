<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->unsignedInteger('minimum_stock')->default(5)->after('stock');
        });
        Schema::create('inventory_order_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(['order_id', 'catalog_product_id']);
        });
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 30);
            $table->integer('quantity');
            $table->unsignedInteger('balance_after');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        $direct = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')->whereNotNull('order_items.catalog_product_id')
            ->selectRaw('order_items.order_id, order_items.catalog_product_id, SUM(order_items.quantity) quantity')
            ->groupBy('order_items.order_id', 'order_items.catalog_product_id')->get();
        $bundles = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('catalog_bundle_items', 'catalog_bundle_items.catalog_bundle_id', '=', 'order_items.catalog_bundle_id')
            ->where('orders.status', '!=', 'cancelled')->whereNotNull('order_items.catalog_bundle_id')
            ->selectRaw('order_items.order_id, catalog_bundle_items.catalog_product_id, SUM(order_items.quantity * catalog_bundle_items.quantity) quantity')
            ->groupBy('order_items.order_id', 'catalog_bundle_items.catalog_product_id')->get();
        $rows = $direct->concat($bundles)->groupBy(fn ($row) => $row->order_id.'-'.$row->catalog_product_id)
            ->map(fn ($group) => ['order_id' => $group->first()->order_id, 'catalog_product_id' => $group->first()->catalog_product_id, 'quantity' => $group->sum('quantity'), 'created_at' => now(), 'updated_at' => now()])->values()->all();
        if ($rows) DB::table('inventory_order_allocations')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_order_allocations');
        Schema::table('catalog_products', fn (Blueprint $table) => $table->dropColumn('minimum_stock'));
    }
};
