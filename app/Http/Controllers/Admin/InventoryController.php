<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\InventoryMovement;
use App\Models\CatalogProductVariant;
use App\Models\InventoryOrderAllocation;
use App\Models\InventoryVariantOrderAllocation;
use App\Models\Order;
use App\Services\InventoryService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = CatalogProduct::query()->with(['photos', 'variants' => fn ($query) => $query->where('is_active', true)->orderBy('color')->orderBy('size')])->withCount([
            'variants' => fn($query) => $query->where('is_active', true),
            'variants as low_variants_count' => fn($query) => $query->where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0),
            'variants as out_variants_count' => fn($query) => $query->where('is_active', true)->where('stock', '<=', 0),
        ])->orderBy('name');
        if ($request->filled('q')) $query->where('name', 'like', '%'.$request->string('q')->trim().'%');
        if ($request->query('status') === 'low') $query->where(fn ($level) => $level->whereHas('variants', fn ($variant) => $variant->where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0))->orWhere(fn ($generic) => $generic->whereDoesntHave('variants', fn ($variant) => $variant->where('is_active', true))->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0)));
        if ($request->query('status') === 'out') $query->where(fn ($level) => $level->whereHas('variants', fn ($variant) => $variant->where('is_active', true)->where('stock', '<=', 0))->orWhere(fn ($generic) => $generic->whereDoesntHave('variants', fn ($variant) => $variant->where('is_active', true))->where('stock', '<=', 0)));

        $pendingNeeds = $this->pendingNeeds();

        return view('admin.inventory.index', [
            'products' => $query->paginate(18)->withQueryString(),
            'pendingNeeds' => $pendingNeeds,
            'pendingOrdersCount' => Order::query()->whereNull('archived_at')->where('status', 'pending')->count(),
            'pendingUnits' => (int) $pendingNeeds->sum('required'),
            'purchaseUnits' => (int) $pendingNeeds->sum('shortage'),
            'stats' => [
                'products' => CatalogProduct::count(),
                'units' => (int) CatalogProduct::sum('stock'),
                'low' => CatalogProductVariant::where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0)->count() + CatalogProduct::whereDoesntHave('variants', fn ($query) => $query->where('is_active', true))->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0)->count(),
                'out' => CatalogProductVariant::where('is_active', true)->where('stock', '<=', 0)->count() + CatalogProduct::whereDoesntHave('variants', fn ($query) => $query->where('is_active', true))->where('stock', '<=', 0)->count(),
            ],
        ]);
    }

    private function pendingNeeds()
    {
        $orders = Order::query()
            ->with([
                'items.product.variants',
                'items.bundle.items.product.variants',
            ])
            ->whereNull('archived_at')
            ->where('status', 'pending')
            ->get();
        $rows = collect();

        $addNeed = function (CatalogProduct $product, int $quantity, string $folio, ?CatalogProductVariant $variant = null, bool $needsAssignment = false) use ($rows) {
            if ($quantity <= 0) {
                return;
            }

            $key = $variant ? 'variant:'.$variant->id : ($needsAssignment ? 'unassigned:' : 'product:').$product->id;
            $existing = $rows->get($key);
            $required = (int) ($existing['required'] ?? 0) + $quantity;
            $available = $needsAssignment ? null : (int) ($variant?->stock ?? $product->stock);
            $remaining = $needsAssignment ? null : $available - $required;

            $rows->put($key, [
                'product' => $product->name,
                'variant' => $variant?->label ?: ($needsAssignment ? 'Sin color o variante asignada' : 'Inventario general'),
                'required' => $required,
                'available_before' => $available,
                'remaining' => $remaining,
                'shortage' => $needsAssignment ? 0 : max(0, -$remaining),
                'needs_assignment' => $needsAssignment,
                'orders' => collect($existing['orders'] ?? [])->push($folio)->filter()->unique()->values(),
            ]);
        };

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $selected = collect($item->selected_variant_ids ?? [])->filter()->countBy();
                    foreach ($selected as $variantId => $quantity) {
                        $variant = $item->product->variants->firstWhere('id', (int) $variantId);
                        if ($variant) {
                            $addNeed($item->product, (int) $quantity, $order->folio, $variant);
                        }
                    }

                    $unassigned = max(0, (int) $item->quantity - (int) $selected->sum());
                    if ($unassigned > 0) {
                        $addNeed($item->product, $unassigned, $order->folio, null, $item->product->variants->isNotEmpty());
                    }
                }

                foreach ($item->bundle?->items ?? [] as $bundleItem) {
                    if (! $bundleItem->product) {
                        continue;
                    }
                    $quantity = (int) $item->quantity * (int) $bundleItem->quantity;
                    $addNeed($bundleItem->product, $quantity, $order->folio, null, $bundleItem->product->variants->isNotEmpty());
                }
            }
        }

        return $rows->sortBy(fn (array $row) => implode('|', [
            $row['shortage'] > 0 ? '0' : ($row['needs_assignment'] ? '1' : '2'),
            mb_strtolower($row['product']),
            mb_strtolower($row['variant']),
        ]))->values();
    }

    public function adjust(Request $request, CatalogProduct $product, InventoryService $inventory): JsonResponse
    {
        $data = $request->validate(['variant_id' => ['nullable', 'integer', 'exists:catalog_product_variants,id'], 'quantity' => ['required', 'integer', 'not_in:0', 'between:-999999,999999'], 'note' => ['required', 'string', 'max:255'], 'minimum_stock' => ['nullable', 'integer', 'min:0', 'max:999999']]);
        $movement = DB::transaction(function () use ($data, $product, $inventory, $request) {
            $variant = filled($data['variant_id'] ?? null) ? $product->variants()->findOrFail($data['variant_id']) : null;
            if (array_key_exists('minimum_stock', $data)) ($variant ?: $product)->update(['minimum_stock' => $data['minimum_stock']]);
            return $variant
                ? $inventory->adjustVariant($product, $variant, (int) $data['quantity'], $data['note'], $request->user())
                : $inventory->adjust($product, (int) $data['quantity'], $data['note'], $request->user());
        });

        return response()->json(['message' => 'Inventario actualizado correctamente.', 'stock' => (int) $product->fresh()->stock, 'selected_stock' => $movement->balance_after, 'minimum_stock' => (int) ($movement->variant?->fresh()->minimum_stock ?? $product->fresh()->minimum_stock)]);
    }

    public function purchaseListPdf(): Response
    {
        $rows = $this->pendingNeeds()
            ->where('shortage', '>', 0)
            ->values();

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.inventory.purchase-list-pdf', [
            'rows' => $rows,
            'totalUnits' => (int) $rows->sum('shortage'),
            'generatedAt' => now(),
        ])->render(), 'UTF-8');
        $dompdf->setPaper('letter');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="lista-de-compras-'.now()->format('Y-m-d').'.pdf"',
        ]);
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
