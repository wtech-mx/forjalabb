<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogBundle;
use App\Models\CatalogProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogBundleController extends Controller
{
    public function index(): View
    {
        return view('admin.catalog-bundles.index', [
            'bundles' => CatalogBundle::query()
                ->withCount('items')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.catalog-bundles.form', [
            'bundle' => new CatalogBundle([
                'is_active' => true,
                'family_multiplier' => 1.50,
                'public_multiplier' => 1.80,
                'sort_order' => (CatalogBundle::max('sort_order') ?? 0) + 10,
            ]),
            'products' => $this->productsForSelect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $bundle = DB::transaction(function () use ($request, $data) {
            $bundle = CatalogBundle::create($this->payload($request, $data));
            $this->syncItems($bundle, $data['items'] ?? []);
            $this->syncPhotos($bundle, $request);
            $this->syncTotals($bundle);

            return $bundle;
        });

        return redirect()
            ->route('admin.packages.preview', $bundle)
            ->with('status', 'Paquete creado correctamente.');
    }

    public function edit(CatalogBundle $package): View
    {
        return view('admin.catalog-bundles.form', [
            'bundle' => $package->load(['items.product', 'photos']),
            'products' => $this->productsForSelect(),
        ]);
    }

    public function update(Request $request, CatalogBundle $package): RedirectResponse
    {
        $data = $this->validated($request, $package);

        DB::transaction(function () use ($request, $package, $data) {
            $package->update($this->payload($request, $data, $package));
            $this->syncItems($package, $data['items'] ?? []);
            $this->syncPhotos($package, $request);
            $this->syncTotals($package);
        });

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'Paquete actualizado correctamente.');
    }

    public function preview(CatalogBundle $package): View
    {
        return view('admin.catalog-bundles.preview', [
            'bundle' => $package->load(['items.product', 'photos']),
        ]);
    }

    public function destroy(CatalogBundle $package): RedirectResponse
    {
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'Paquete eliminado.');
    }

    private function validated(Request $request, ?CatalogBundle $bundle = null): array
    {
        $bundleId = $bundle?->id;
        $request->merge([
            'slug' => $this->uniqueSlug($request->input('name'), $bundleId),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'slug' => ['required', 'string', 'max:160', Rule::unique('catalog_bundles', 'slug')->ignore($bundleId)],
            'description' => ['nullable', 'string', 'max:900'],
            'packaging_cost' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'family_multiplier' => ['nullable', 'numeric', 'min:1', 'max:99'],
            'public_multiplier' => ['nullable', 'numeric', 'min:1', 'max:99'],
            'cover_photo' => ['nullable', 'image', 'max:8192'],
            'gallery_photos' => ['nullable', 'array'],
            'gallery_photos.*' => ['nullable', 'image', 'max:8192'],
            'items' => ['nullable', 'array'],
            'items.*.catalog_product_id' => ['nullable', 'integer', Rule::exists('catalog_products', 'id')],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:999999'],
        ]);
    }

    private function payload(Request $request, array $data, ?CatalogBundle $bundle = null): array
    {
        $coverPhotoPath = $bundle?->cover_photo_path;
        if ($request->hasFile('cover_photo')) {
            $coverPhotoPath = $this->storePublicBundleImage(
                $request->file('cover_photo'),
                $data['slug'],
                'portada'
            );
        }

        return [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'packaging_cost' => (float) ($data['packaging_cost'] ?? 0),
            'family_multiplier' => (float) ($data['family_multiplier'] ?? 1.5),
            'public_multiplier' => (float) ($data['public_multiplier'] ?? 1.8),
            'cover_photo_path' => $coverPhotoPath,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $bundle?->sort_order ?: (CatalogBundle::max('sort_order') ?? 0) + 10,
        ];
    }

    private function syncItems(CatalogBundle $bundle, array $items): void
    {
        $bundle->items()->delete();
        $products = CatalogProduct::query()
            ->whereIn('id', collect($items)->pluck('catalog_product_id')->filter())
            ->get()
            ->keyBy('id');

        collect($items)
            ->filter(fn (array $item) => filled($item['catalog_product_id'] ?? null))
            ->values()
            ->each(function (array $item, int $index) use ($bundle, $products) {
                $product = $products->get((int) $item['catalog_product_id']);
                if (! $product) {
                    return;
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $unitCost = (float) $product->cost_subtotal;

                $bundle->items()->create([
                    'catalog_product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => round($unitCost * $quantity, 2),
                    'sort_order' => ($index + 1) * 10,
                ]);
            });
    }

    private function syncPhotos(CatalogBundle $bundle, Request $request): void
    {
        if (! $request->hasFile('gallery_photos')) {
            return;
        }

        foreach ($request->file('gallery_photos') as $file) {
            if (! $file) {
                continue;
            }

            $bundle->photos()->create([
                'image_path' => $this->storePublicBundleImage($file, $bundle->slug, 'galeria'),
                'sort_order' => (($bundle->photos()->max('sort_order') ?? 0) + 10),
            ]);
        }
    }

    private function syncTotals(CatalogBundle $bundle): void
    {
        $itemsCost = (float) $bundle->items()->sum('total_cost');
        $packagingCost = (float) $bundle->packaging_cost;
        $totalCost = round($itemsCost + $packagingCost, 2);
        $familyPrice = $this->roundPriceUp($totalCost * (float) $bundle->family_multiplier);
        $publicPrice = $this->roundPriceUp($totalCost * (float) $bundle->public_multiplier);

        $bundle->update([
            'items_cost' => $itemsCost,
            'total_cost' => $totalCost,
            'family_price' => $familyPrice,
            'family_profit' => round($familyPrice - $totalCost, 2),
            'public_price' => $publicPrice,
            'public_profit' => round($publicPrice - $totalCost, 2),
        ]);
    }

    private function roundPriceUp(float $amount): float
    {
        return (float) ceil($amount);
    }

    private function productsForSelect()
    {
        return CatalogProduct::query()
            ->orderBy('name')
            ->get(['id', 'name', 'cost_subtotal', 'public_price']);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'paquete';
        $slug = $base;
        $counter = 2;

        while (CatalogBundle::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function storePublicBundleImage($file, string $bundleSlug, string $folder): string
    {
        $directory = public_path("images/catalog/packages/{$bundleSlug}/{$folder}");
        File::ensureDirectoryExists($directory);

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $storedName = ($filename ?: 'imagen').'-'.now()->format('YmdHis').'-'.Str::random(6).'.'.$extension;

        $file->move($directory, $storedName);

        return "images/catalog/packages/{$bundleSlug}/{$folder}/{$storedName}";
    }
}
