<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('catalog_products')->whereExists(function ($query) {
            $query->selectRaw('1')->from('catalog_product_variants')->whereColumn('catalog_product_variants.catalog_product_id', 'catalog_products.id');
        })->update([
            'stock' => DB::raw('(select coalesce(sum(stock), 0) from catalog_product_variants where catalog_product_variants.catalog_product_id = catalog_products.id and is_active = 1)'),
        ]);
    }

    public function down(): void {}
};
