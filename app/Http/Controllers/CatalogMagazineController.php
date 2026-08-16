<?php

namespace App\Http\Controllers;

use App\Models\CatalogBundle;
use App\Models\CatalogProduct;
use Illuminate\View\View;

class CatalogMagazineController extends Controller
{
    public function __invoke(bool $showPrices = true): View
    {
        return view('catalog.magazine', [
            'products' => CatalogProduct::active()->with(['photos', 'options', 'salePackages'])->orderBy('sort_order')->orderBy('name')->get(),
            'bundles' => CatalogBundle::active()->with(['items.product', 'photos'])->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name')->get(),
            'showPrices' => $showPrices,
        ]);
    }
}
