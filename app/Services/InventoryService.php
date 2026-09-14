<?php

namespace App\Services;

use App\Models\CatalogProduct;
use App\Models\CatalogProductVariant;
use App\Models\InventoryMovement;
use App\Models\InventoryOrderAllocation;
use App\Models\InventoryVariantOrderAllocation;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function syncOrder(Order $order, ?User $user = null): void
    {
        $order->loadMissing(['items.bundle.items']);
        $desired = $order->status === 'cancelled' ? collect() : $this->requirements($order);
        $desiredVariants = $order->status === 'cancelled' ? collect() : $this->variantRequirements($order);
        $allocations = InventoryOrderAllocation::where('order_id', $order->id)->lockForUpdate()->get()->keyBy('catalog_product_id');
        $variantAllocations = InventoryVariantOrderAllocation::where('order_id', $order->id)->lockForUpdate()->get()->keyBy('catalog_product_variant_id');
        $productMovements = collect();

        foreach ($desired->keys()->merge($allocations->keys())->unique()->sort() as $productId) {
            $product = CatalogProduct::whereKey($productId)->lockForUpdate()->first();
            if (! $product) continue;
            $allocation = $allocations->get($productId);
            $oldQuantity = (int) ($allocation?->quantity ?? 0);
            $newQuantity = (int) $desired->get($productId, 0);
            $movement = $oldQuantity - $newQuantity;
            if ($movement === 0) continue;
            $newBalance = (int) $product->stock + $movement;
            $product->update(['stock' => $newBalance]);
            $productMovements[$product->id] = ['movement'=>$movement, 'balance'=>$newBalance];
            if ($newQuantity > 0) InventoryOrderAllocation::updateOrCreate(['order_id'=>$order->id,'catalog_product_id'=>$product->id], ['quantity'=>$newQuantity]);
            else $allocation?->delete();
        }

        $variantMovementByProduct = collect();
        foreach ($desiredVariants->keys()->merge($variantAllocations->keys())->unique()->sort() as $variantId) {
            $variant = CatalogProductVariant::with('product')->whereKey($variantId)->lockForUpdate()->first();
            if (! $variant) continue;
            $allocation = $variantAllocations->get($variantId);
            $oldQuantity = (int) ($allocation?->quantity ?? 0);
            $newQuantity = (int) $desiredVariants->get($variantId, 0);
            $movement = $oldQuantity - $newQuantity;
            if ($movement === 0) continue;
            $newBalance = (int) $variant->stock + $movement;
            $variant->update(['stock' => $newBalance]);
            InventoryMovement::create(['catalog_product_id'=>$variant->catalog_product_id,'catalog_product_variant_id'=>$variant->id,'order_id'=>$order->id,'created_by'=>$user?->id,'type'=>$movement<0?'order_out':'order_return','quantity'=>$movement,'balance_after'=>$newBalance,'note'=>($movement<0?'Salida':'Devolución').' de '.$variant->label.' · '.$order->folio]);
            $variantMovementByProduct[$variant->catalog_product_id] = (int) $variantMovementByProduct->get($variant->catalog_product_id, 0) + $movement;
            if ($newQuantity > 0) InventoryVariantOrderAllocation::updateOrCreate(['order_id'=>$order->id,'catalog_product_variant_id'=>$variant->id], ['quantity'=>$newQuantity]);
            else $allocation?->delete();
        }

        foreach ($productMovements as $productId => $data) {
            $genericMovement = $data['movement'] - (int) $variantMovementByProduct->get($productId, 0);
            if ($genericMovement === 0) continue;
            InventoryMovement::create(['catalog_product_id'=>$productId,'order_id'=>$order->id,'created_by'=>$user?->id,'type'=>$genericMovement<0?'order_out':'order_return','quantity'=>$genericMovement,'balance_after'=>$data['balance'],'note'=>$genericMovement<0?'Salida por pedido '.$order->folio:'Devolución por ajuste del pedido '.$order->folio]);
        }
    }

    public function adjust(CatalogProduct $product, int $quantity, string $note, ?User $user = null): InventoryMovement
    {
        return DB::transaction(function () use ($product, $quantity, $note, $user) {
            $product = CatalogProduct::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $newBalance = (int) $product->stock + $quantity;
            $product->update(['stock' => $newBalance]);
            return InventoryMovement::create(['catalog_product_id'=>$product->id,'created_by'=>$user?->id,'type'=>$quantity>0?'manual_entry':'manual_out','quantity'=>$quantity,'balance_after'=>$newBalance,'note'=>$note]);
        });
    }

    public function adjustVariant(CatalogProduct $product, CatalogProductVariant $variant, int $quantity, string $note, ?User $user = null): InventoryMovement
    {
        return DB::transaction(function () use ($product, $variant, $quantity, $note, $user) {
            $product = CatalogProduct::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $variant = CatalogProductVariant::whereKey($variant->id)->where('catalog_product_id', $product->id)->lockForUpdate()->firstOrFail();
            $variantBalance = (int) $variant->stock + $quantity;
            $productBalance = (int) $product->stock + $quantity;
            $variant->update(['stock' => $variantBalance]);
            $product->update(['stock' => $productBalance]);

            return InventoryMovement::create([
                'catalog_product_id' => $product->id, 'catalog_product_variant_id' => $variant->id,
                'created_by' => $user?->id, 'type' => $quantity > 0 ? 'manual_entry' : 'manual_out',
                'quantity' => $quantity, 'balance_after' => $variantBalance, 'note' => $note.' · '.$variant->label,
            ]);
        });
    }

    private function requirements(Order $order): Collection
    {
        $requirements = collect();
        foreach ($order->items as $item) {
            if ($item->catalog_product_id) $requirements[$item->catalog_product_id] = (int) $requirements->get($item->catalog_product_id, 0) + (int) $item->quantity;
            foreach ($item->bundle?->items ?? [] as $bundleItem) $requirements[$bundleItem->catalog_product_id] = (int) $requirements->get($bundleItem->catalog_product_id, 0) + ((int) $item->quantity * (int) $bundleItem->quantity);
        }
        return $requirements;
    }

    private function variantRequirements(Order $order): Collection
    {
        return $order->items->flatMap(fn ($item) => $item->selected_variant_ids ?? [])->filter()->countBy()->map(fn ($quantity) => (int) $quantity);
    }
}
