<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\InventoryMovement;
use App\Models\CatalogProductVariant;
use App\Models\InventoryOrderAllocation;
use App\Models\InventoryVariantOrderAllocation;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = CatalogProduct::query()->with('photos')->withCount([
            'variants' => fn($query) => $query->where('is_active', true),
            'variants as low_variants_count' => fn($query) => $query->where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0),
            'variants as out_variants_count' => fn($query) => $query->where('is_active', true)->where('stock', '<=', 0),
        ])->orderBy('name');
        if ($request->filled('q')) $query->where('name', 'like', '%'.$request->string('q')->trim().'%');
        if ($request->query('status') === 'low') $query->where(fn ($level) => $level->whereHas('variants', fn ($variant) => $variant->where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0))->orWhere(fn ($generic) => $generic->whereDoesntHave('variants', fn ($variant) => $variant->where('is_active', true))->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0)));
        if ($request->query('status') === 'out') $query->where(fn ($level) => $level->whereHas('variants', fn ($variant) => $variant->where('is_active', true)->where('stock', '<=', 0))->orWhere(fn ($generic) => $generic->whereDoesntHave('variants', fn ($variant) => $variant->where('is_active', true))->where('stock', '<=', 0)));

        return view('admin.inventory.index', [
            'products' => $query->paginate(18)->withQueryString(),
            'recentMovements' => InventoryMovement::with(['product', 'variant', 'order', 'creator'])->latest()->limit(15)->get(),
            'stats' => [
                'products' => CatalogProduct::count(),
                'units' => (int) CatalogProduct::sum('stock'),
                'low' => CatalogProductVariant::where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0)->count() + CatalogProduct::whereDoesntHave('variants', fn ($query) => $query->where('is_active', true))->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0)->count(),
                'out' => CatalogProductVariant::where('is_active', true)->where('stock', '<=', 0)->count() + CatalogProduct::whereDoesntHave('variants', fn ($query) => $query->where('is_active', true))->where('stock', '<=', 0)->count(),
            ],
        ]);
    }

    public function adjust(Request $request, CatalogProduct $product, InventoryService $inventory): JsonResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'not_in:0', 'between:-999999,999999'], 'note' => ['required', 'string', 'max:255'], 'minimum_stock' => ['nullable', 'integer', 'min:0', 'max:999999']]);
        $movement = DB::transaction(function () use ($data, $product, $inventory, $request) {
            if (array_key_exists('minimum_stock', $data)) $product->update(['minimum_stock' => $data['minimum_stock']]);
            return $inventory->adjust($product, (int) $data['quantity'], $data['note'], $request->user());
        });

        return response()->json(['message' => 'Inventario actualizado correctamente.', 'stock' => $movement->balance_after, 'minimum_stock' => (int) $product->fresh()->minimum_stock]);
    }

    public function variants(CatalogProduct $product): View
    {
        return view('admin.inventory.variants', ['product' => $product->load('variants')]);
    }

    public function syncVariants(Request $request, CatalogProduct $product): RedirectResponse
    {
        $data = $request->validate([
            'variants' => ['required','array','min:1'], 'variants.*.id' => ['nullable','integer'], 'variants.*.sku' => ['nullable','string','max:80'],
            'variants.*.color' => ['nullable','string','max:80'], 'variants.*.size' => ['nullable','string','max:40'], 'variants.*.stock' => ['required','integer','between:-999999,999999'],
            'variants.*.minimum_stock' => ['required','integer','min:0','max:999999'], 'variants.*.is_active' => ['nullable','boolean'],
        ]);
        DB::transaction(function () use ($data, $product, $request) {
            $kept = [];
            foreach ($data['variants'] as $index => $row) {
                abort_if(blank($row['color'] ?? null) && blank($row['size'] ?? null), 422, 'Cada variante necesita color, talla o ambos.');
                $duplicate = collect($data['variants'])->keys()->contains(fn($other) => $other < $index && mb_strtolower(trim($data['variants'][$other]['color'] ?? '')) === mb_strtolower(trim($row['color'] ?? '')) && mb_strtolower(trim($data['variants'][$other]['size'] ?? '')) === mb_strtolower(trim($row['size'] ?? '')));
                abort_if($duplicate, 422, 'Hay combinaciones de color y talla repetidas.');
                $variant = filled($row['id'] ?? null) ? $product->variants()->findOrFail($row['id']) : new CatalogProductVariant;
                $oldStock = (int) ($variant->stock ?? 0);
                $sku = trim($row['sku'] ?? '') ?: $this->uniqueSku($product, $row, $variant->id);
                $variant->fill(['sku'=>$sku,'color'=>filled($row['color']??null)?trim($row['color']):null,'size'=>filled($row['size']??null)?trim($row['size']):null,'stock'=>$row['stock'],'minimum_stock'=>$row['minimum_stock'],'is_active'=>(bool)($row['is_active']??false)]);
                $product->variants()->save($variant); $kept[] = $variant->id;
                $difference = (int) $variant->stock - $oldStock;
                if ($difference !== 0) InventoryMovement::create(['catalog_product_id'=>$product->id,'catalog_product_variant_id'=>$variant->id,'created_by'=>$request->user()?->id,'type'=>$difference>0?'manual_entry':'manual_out','quantity'=>$difference,'balance_after'=>$variant->stock,'note'=>'Ajuste en matriz · '.$variant->label]);
            }
            $product->variants()->whereNotIn('id', $kept)->update(['is_active'=>false]);
            $allocated = (int) InventoryOrderAllocation::where('catalog_product_id', $product->id)->sum('quantity');
            $allocatedToVariants = (int) InventoryVariantOrderAllocation::whereHas('variant', fn ($query) => $query->where('catalog_product_id', $product->id))->sum('quantity');
            $unassigned = max(0, $allocated - $allocatedToVariants);
            $product->update(['stock'=>(int)$product->variants()->where('is_active',true)->sum('stock') - $unassigned]);
        });
        return back()->with('status','Matriz de variantes actualizada correctamente.');
    }

    private function uniqueSku(CatalogProduct $product, array $row, ?int $ignore = null): string
    {
        $base = strtoupper(Str::slug($product->name.'-'.($row['color']??'').'-'.($row['size']??''), '-')) ?: 'VAR-'.$product->id;
        $sku = substr($base,0,70); $suffix=1;
        while (CatalogProductVariant::where('sku',$sku)->when($ignore,fn($q)=>$q->where('id','!=',$ignore))->exists()) $sku=substr($base,0,65).'-'.++$suffix;
        return $sku;
    }
}
