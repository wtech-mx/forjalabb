<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\CatalogProductOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogProductController extends Controller
{
    public function index(): View
    {
        return view('admin.catalog-products.index', [
            'products' => CatalogProduct::query()
                ->with(['costs', 'salePackages'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.catalog-products.form', [
            'product' => new CatalogProduct([
                'is_active' => true,
                'presentation_mode' => CatalogProduct::MODE_GALLERY,
                'action_label' => 'Cotizar',
                'sort_order' => (CatalogProduct::max('sort_order') ?? 0) + 10,
            ]),
            'presentationModes' => CatalogProduct::PRESENTATION_MODES,
            'optionGroups' => CatalogProductOption::GROUPS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $product = DB::transaction(function () use ($request, $data) {
            $product = CatalogProduct::create($this->productPayload($request, $data));
            $this->syncCosts($product, $data['costs'] ?? []);
            $this->syncSalePackages($product, $data['sale_packages'] ?? []);
            $this->syncPhotos($product, $request);
            $this->syncOptions($product, $request, $data);
            $this->syncProductMainPrice($product);

            return $product;
        });

        return redirect()
            ->route('admin.catalog.preview', $product)
            ->with('status', 'Producto creado correctamente.');
    }

    public function edit(CatalogProduct $catalog): View
    {
        return view('admin.catalog-products.form', [
            'product' => $catalog->load(['costs', 'options', 'salePackages', 'photos']),
            'presentationModes' => CatalogProduct::PRESENTATION_MODES,
            'optionGroups' => CatalogProductOption::GROUPS,
        ]);
    }

    public function update(Request $request, CatalogProduct $catalog): RedirectResponse
    {
        $data = $this->validated($request, $catalog);
        DB::transaction(function () use ($request, $catalog, $data) {
            $catalog->update($this->productPayload($request, $data, $catalog));
            $this->syncCosts($catalog, $data['costs'] ?? []);
            $this->syncSalePackages($catalog, $data['sale_packages'] ?? []);
            $this->syncPhotos($catalog, $request);
            $this->syncOptions($catalog, $request, $data);
            $this->syncProductMainPrice($catalog);
        });

        return redirect()
            ->route('admin.catalog.index')
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function preview(CatalogProduct $catalog): View
    {
        return view('admin.catalog-products.preview', [
            'product' => $catalog->load(['costs', 'options', 'salePackages', 'photos']),
        ]);
    }

    public function destroy(CatalogProduct $catalog): RedirectResponse
    {
        $catalog->delete();

        return redirect()
            ->route('admin.catalog.index')
            ->with('status', 'Producto eliminado.');
    }

    private function validated(Request $request, ?CatalogProduct $product = null): array
    {
        $productId = $product?->id;
        $request->merge([
            'slug' => $this->uniqueSlug($request->input('name'), $productId),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'slug' => ['required', 'string', 'max:160', Rule::unique('catalog_products', 'slug')->ignore($productId)],
            'description' => ['nullable', 'string', 'max:700'],
            'cover_photo' => ['nullable', 'image', 'max:8192'],
            'gallery_photos' => ['nullable', 'array'],
            'gallery_photos.*' => ['nullable', 'image', 'max:8192'],
            'presentation_mode' => ['required', Rule::in(array_keys(CatalogProduct::PRESENTATION_MODES))],
            'stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'costs' => ['nullable', 'array'],
            'costs.*.name' => ['nullable', 'string', 'max:140'],
            'costs.*.cost' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'sale_packages' => ['nullable', 'array'],
            'sale_packages.*.name' => ['nullable', 'string', 'max:140'],
            'sale_packages.*.quantity' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'sale_packages.*.public_multiplier' => ['nullable', 'numeric', 'min:1', 'max:99'],
            'sale_packages.*.family_multiplier' => ['nullable', 'numeric', 'min:1', 'max:99'],
            'sale_packages.*.packaging_cost' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'sale_packages.*.is_default' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'options.*.group' => ['nullable', Rule::in(array_keys(CatalogProductOption::GROUPS))],
            'options.*.name' => ['nullable', 'string', 'max:140'],
            'options.*.stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'options.*.image' => ['nullable', 'image', 'max:8192'],
            'options.*.existing_image_path' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function productPayload(Request $request, array $data, ?CatalogProduct $product = null): array
    {
        $costSubtotal = collect($data['costs'] ?? [])
            ->filter(fn (array $cost) => filled($cost['name'] ?? null))
            ->sum(fn (array $cost) => (float) ($cost['cost'] ?? 0));
        $friendsPrice = round($costSubtotal * 1.5, 2);
        $publicPrice = round($costSubtotal * 1.8, 2);

        $coverPhotoPath = $product?->cover_photo_path ?: $product?->image_path;
        if ($request->hasFile('cover_photo')) {
            $coverPhotoPath = $this->storePublicCatalogImage(
                $request->file('cover_photo'),
                $data['slug'],
                'portada'
            );
        }

        return [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'cost_subtotal' => $costSubtotal,
            'friends_price' => $friendsPrice,
            'public_price' => $publicPrice,
            'friends_profit' => round($friendsPrice - $costSubtotal, 2),
            'public_profit' => round($publicPrice - $costSubtotal, 2),
            'stock' => $data['presentation_mode'] === CatalogProduct::MODE_GALLERY
                ? (int) ($data['stock'] ?? 0)
                : $this->totalColorStock($data['options'] ?? []),
            'cover_photo_path' => $coverPhotoPath,
            'presentation_mode' => $data['presentation_mode'],
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $product?->sort_order ?: (CatalogProduct::max('sort_order') ?? 0) + 10,
            'action_label' => 'Ver producto',
            'presentation' => CatalogProduct::PRESENTATION_PHOTO,
            'url' => null,
            'icon' => null,
        ];
    }

    private function syncCosts(CatalogProduct $product, array $costs): void
    {
        $product->costs()->delete();

        collect($costs)
            ->filter(fn (array $cost) => filled($cost['name'] ?? null))
            ->values()
            ->each(fn (array $cost, int $index) => $product->costs()->create([
                'name' => $cost['name'],
                'cost' => (float) ($cost['cost'] ?? 0),
                'sort_order' => ($index + 1) * 10,
            ]));
    }

    private function syncPhotos(CatalogProduct $product, Request $request): void
    {
        if (! $request->hasFile('gallery_photos')) {
            return;
        }

        foreach ($request->file('gallery_photos') as $file) {
            if (! $file) {
                continue;
            }

            $product->photos()->create([
                'image_path' => $this->storePublicCatalogImage($file, $product->slug, 'galeria'),
                'sort_order' => (($product->photos()->max('sort_order') ?? 0) + 10),
            ]);
        }
    }

    private function syncOptions(CatalogProduct $product, Request $request, array $data): void
    {
        $product->options()->delete();
        if (($data['presentation_mode'] ?? null) === CatalogProduct::MODE_GALLERY) {
            return;
        }

        $options = $data['options'] ?? [];

        collect($options)
            ->filter(fn (array $option) => filled($option['name'] ?? null))
            ->each(function (array $option, int|string $index) use ($product, $request) {
                $imagePath = $option['existing_image_path'] ?? null;
                if ($request->hasFile("options.$index.image")) {
                    $imagePath = $this->storePublicCatalogImage(
                        $request->file("options.$index.image"),
                        $product->slug,
                        'opciones'
                    );
                }

                $product->options()->create([
                    'group' => $option['group'] ?: 'tipo',
                    'name' => $option['name'],
                    'image_path' => $imagePath,
                    'stock' => ($option['group'] ?? null) === 'color' ? (int) ($option['stock'] ?? 0) : 0,
                    'sort_order' => ((int) $product->options()->count() + 1) * 10,
                ]);
            });
    }

    private function syncSalePackages(CatalogProduct $product, array $packages): void
    {
        $product->salePackages()->delete();
        $unitCost = (float) $product->cost_subtotal;
        $hasDefault = false;

        collect($packages)
            ->filter(fn (array $package) => filled($package['name'] ?? null))
            ->values()
            ->each(function (array $package, int $index) use ($product, $unitCost, &$hasDefault) {
                $quantity = max(1, (int) ($package['quantity'] ?? 1));
                $publicMultiplier = max(1, (float) ($package['public_multiplier'] ?? 1.8));
                $familyMultiplier = max(1, (float) ($package['family_multiplier'] ?? 1.5));
                $packagingCost = max(0, (float) ($package['packaging_cost'] ?? 0));
                $totalCost = round(($unitCost * $quantity) + $packagingCost, 2);
                $familyPrice = round($totalCost * $familyMultiplier, 2);
                $publicPrice = round($totalCost * $publicMultiplier, 2);
                $isDefault = ! $hasDefault && ! empty($package['is_default']);
                $hasDefault = $hasDefault || $isDefault;

                $product->salePackages()->create([
                    'name' => $package['name'],
                    'quantity' => $quantity,
                    'public_multiplier' => $publicMultiplier,
                    'family_multiplier' => $familyMultiplier,
                    'unit_cost' => $unitCost,
                    'packaging_cost' => $packagingCost,
                    'total_cost' => $totalCost,
                    'unit_family_price' => round($familyPrice / $quantity, 2),
                    'family_price' => $familyPrice,
                    'family_profit' => round($familyPrice - $totalCost, 2),
                    'unit_public_price' => round($publicPrice / $quantity, 2),
                    'public_price' => $publicPrice,
                    'public_profit' => round($publicPrice - $totalCost, 2),
                    'is_default' => $isDefault,
                    'sort_order' => ($index + 1) * 10,
                ]);
            });

        if (! $product->salePackages()->exists()) {
            $product->salePackages()->create([
                'name' => 'Individual',
                'quantity' => 1,
                'public_multiplier' => 1.8,
                'family_multiplier' => 1.5,
                'unit_cost' => $unitCost,
                'packaging_cost' => 0,
                'total_cost' => $unitCost,
                'unit_family_price' => round($unitCost * 1.5, 2),
                'family_price' => round($unitCost * 1.5, 2),
                'family_profit' => round(($unitCost * 1.5) - $unitCost, 2),
                'unit_public_price' => round($unitCost * 1.8, 2),
                'public_price' => round($unitCost * 1.8, 2),
                'public_profit' => round(($unitCost * 1.8) - $unitCost, 2),
                'is_default' => true,
                'sort_order' => 10,
            ]);
        } elseif (! $hasDefault) {
            $product->salePackages()->orderBy('sort_order')->first()?->update(['is_default' => true]);
        }
    }

    private function syncProductMainPrice(CatalogProduct $product): void
    {
        $package = $product->salePackages()
            ->where('is_default', true)
            ->first() ?: $product->salePackages()->orderBy('sort_order')->first();

        if (! $package) {
            return;
        }

        $product->update([
            'friends_price' => $package->family_price,
            'friends_profit' => $package->family_profit,
            'public_price' => $package->public_price,
            'public_profit' => $package->public_profit,
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'producto';
        $slug = $base;
        $counter = 2;

        while (CatalogProduct::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function storePublicCatalogImage($file, string $productSlug, string $folder): string
    {
        $directory = public_path("images/catalog/uploads/{$productSlug}/{$folder}");
        File::ensureDirectoryExists($directory);

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $storedName = ($filename ?: 'imagen').'-'.now()->format('YmdHis').'-'.Str::random(6).'.'.$extension;

        $file->move($directory, $storedName);

        return "images/catalog/uploads/{$productSlug}/{$folder}/{$storedName}";
    }

    private function totalColorStock(array $options): int
    {
        return collect($options)
            ->filter(fn (array $option) => ($option['group'] ?? null) === 'color' && filled($option['name'] ?? null))
            ->sum(fn (array $option) => (int) ($option['stock'] ?? 0));
    }
}
