<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductPersonalizationController extends Controller
{
    public function index(): View
    {
        $products = CatalogProduct::query()
            ->with('photos')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(fn (CatalogProduct $product) => ($product->cover_photo_path ?: $product->image_path) || $product->photos->isNotEmpty())
            ->values();

        return view('admin.product-personalization.index', [
            'products' => $products,
            'templates' => $products->map(fn (CatalogProduct $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'enabled' => $product->is_personalizable,
                'editUrl' => route('admin.catalog.edit', $product),
                'method' => $product->personalization_method ?: CatalogProduct::PERSONALIZATION_LASER,
                'x' => $product->personalization_x,
                'y' => $product->personalization_y,
                'width' => $product->personalization_width,
                'height' => $product->personalization_height,
                'rotation' => $product->personalization_rotation,
                'fontSize' => $product->personalization_font_size,
                'fontFamily' => $product->personalization_font_family,
                'textColor' => $product->personalization_text_color,
                'images' => collect([$product->cover_photo_path ?: $product->image_path])
                    ->merge($product->photos->pluck('image_path'))
                    ->filter()
                    ->map(fn (string $path) => Str::startsWith($path, ['http://', 'https://']) ? $path : '/'.ltrim($path, '/'))
                    ->values()
                    ->all(),
            ])->values(),
        ]);
    }
}
