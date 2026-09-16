<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $productAllocations = DB::table('inventory_order_allocations')
                ->join('orders', 'orders.id', '=', 'inventory_order_allocations.order_id')
                ->where('orders.status', '!=', 'in_progress')
                ->select('inventory_order_allocations.id', 'inventory_order_allocations.catalog_product_id', 'inventory_order_allocations.quantity')
                ->get();

            foreach ($productAllocations as $allocation) {
                DB::table('catalog_products')->where('id', $allocation->catalog_product_id)->increment('stock', $allocation->quantity);
                DB::table('inventory_order_allocations')->where('id', $allocation->id)->delete();
            }

            $variantAllocations = DB::table('inventory_variant_order_allocations')
                ->join('orders', 'orders.id', '=', 'inventory_variant_order_allocations.order_id')
                ->where('orders.status', '!=', 'in_progress')
                ->select('inventory_variant_order_allocations.id', 'inventory_variant_order_allocations.catalog_product_variant_id', 'inventory_variant_order_allocations.quantity')
                ->get();

            foreach ($variantAllocations as $allocation) {
                DB::table('catalog_product_variants')->where('id', $allocation->catalog_product_variant_id)->increment('stock', $allocation->quantity);
                DB::table('inventory_variant_order_allocations')->where('id', $allocation->id)->delete();
            }
        });
    }

    public function down(): void
    {
        // La reserva historica no puede reconstruirse con seguridad al revertir.
    }
};
