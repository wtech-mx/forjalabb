<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CatalogQuoteController extends Controller
{
    public function index(): View
    {
        return view('admin.catalog-quotes.index', [
            'products' => CatalogProduct::with('salePackages')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function pdf(Request $request): Response
    {
        $data = $request->validate([
            'recipient' => ['nullable', 'string', 'max:160'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('catalog_products', 'id')],
            'items.*.price' => ['required', 'numeric', 'min:0', 'max:9999999.99', 'decimal:0,2'],
            'items.*.minimum_quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
        ], [
            'items.required' => 'Selecciona al menos un producto para generar el PDF.',
            'items.*.price.required' => 'Indica el precio ofrecido de cada producto seleccionado.',
            'items.*.price.decimal' => 'El precio puede tener hasta dos decimales.',
            'items.*.minimum_quantity.min' => 'El mayoreo debe comenzar a partir de al menos una pieza.',
        ]);

        $products = CatalogProduct::with('salePackages')->whereIn('id', array_column($data['items'], 'product_id'))->get()->keyBy('id');
        $rows = collect($data['items'])->map(function (array $item) use ($products) {
            $product = $products->get($item['product_id']);
            abort_unless($product, 422, 'Un producto seleccionado ya no está disponible.');
            $package = $product->salePackages->firstWhere('is_default', true) ?: $product->salePackages->first();

            return [
                'product' => $product,
                'image' => $this->pdfImageSource($product),
                'presentation' => $package ? $package->name.' ('.$package->quantity.' piezas)' : '1 pieza',
                'public_price' => $package?->public_price ?? $product->public_price,
                'price' => $item['price'],
                'minimum_quantity' => $item['minimum_quantity'],
            ];
        });

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('chroot', public_path());
        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('admin.catalog-quotes.pdf', [
            'rows' => $rows,
            'recipient' => $data['recipient'] ?? null,
            'notes' => $data['notes'] ?? null,
            'generatedAt' => now(),
        ])->render(), 'UTF-8');
        $pdf->setPaper('letter');
        $pdf->render();
        $pdf->getCanvas()->page_text(40, 755, 'ForjaLab | Página {PAGE_NUM} de {PAGE_COUNT}', null, 8, [0.4, 0.4, 0.4]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="catalogo-mayoreo-'.now()->format('Y-m-d').'.pdf"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function pdfImageSource(CatalogProduct $product): ?string
    {
        $path = $product->cover_photo_path ?: $product->image_path;
        if (! $path) {
            return null;
        }

        $publicDirectory = realpath(public_path());
        $absolutePath = realpath(public_path(ltrim($path, '/\\')));
        if (! $publicDirectory || ! $absolutePath || ! is_file($absolutePath)
            || ! str_starts_with($absolutePath, $publicDirectory.DIRECTORY_SEPARATOR)) {
            return null;
        }

        $mimeType = mime_content_type($absolutePath);
        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'], true)) {
            return null;
        }

        return 'data:'.$mimeType.';base64,'.base64_encode(file_get_contents($absolutePath));
    }
}
