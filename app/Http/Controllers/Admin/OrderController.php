<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\CatalogProductSalePackage;
use App\Models\CatalogBundle;
use App\Models\Customer;
use App\Models\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $showArchived = $request->boolean('archived');
        $deliveryDate = $request->date('delivery_date')?->format('Y-m-d');

        return view('admin.orders.index', [
            'orders' => Order::with('customer')
            ->when($showArchived, fn ($query) => $query->whereNotNull('archived_at'), fn ($query) => $query->whereNull('archived_at'))
            ->when($deliveryDate, fn ($query) => $query->whereDate('delivery_at', $deliveryDate))
            ->when($search, function ($query) use ($search) {
                $query->where(fn ($q) => $q->where('folio', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")));
            })->orderByRaw('delivery_at is null')->orderBy('delivery_at')->latest('ordered_at')->paginate(15)->withQueryString(),
            'search' => $search,
            'showArchived' => $showArchived,
            'deliveryDate' => $deliveryDate,
        ]);
    }

    public function create(): View
    {
        return $this->form(new Order(['ordered_at' => now(), 'status' => 'pending', 'discount_type' => 'fixed']));
    }

    public function store(Request $request): RedirectResponse
    {
        $order = DB::transaction(fn () => $this->saveOrder(new Order, $request));

        return redirect()->route('admin.orders.show', $order)->with('status', 'Pedido creado correctamente. Ya puedes descargar su PDF.');
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load(['customer', 'items', 'references', 'creator', 'shipment.events.media'])]);
    }

    public function edit(Order $order): View
    {
        return $this->form($order->load(['items', 'references']));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        DB::transaction(fn () => $this->saveOrder($order, $request));

        return redirect()->route('admin.orders.show', $order)->with('status', 'Pedido actualizado correctamente.');
    }

    public function archive(Order $order): RedirectResponse
    {
        $order->forceFill(['archived_at' => now()])->save();

        return redirect()->route('admin.orders.index')->with('status', 'Pedido archivado correctamente.');
    }

    public function restore(Order $order): RedirectResponse
    {
        $order->forceFill(['archived_at' => null])->save();

        return redirect()->route('admin.orders.show', $order)->with('status', 'Pedido restaurado correctamente.');
    }

    public function pdf(Order $order): Response
    {
        $order->load(['customer', 'items.product', 'items.bundle', 'references', 'creator']);
        $order->items->each(function ($item) {
            $path = $item->item_type === 'bundle'
                ? $item->bundle?->cover_photo_path
                : ($item->product?->cover_photo_path ?: $item->product?->image_path);
            $item->pdf_image_source = $this->pdfImageSource($path);
        });
        $logoSource = $this->pdfImageSource('icon-192.png');
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', public_path());
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.orders.pdf', compact('order', 'logoSource'))->render(), 'UTF-8');
        $dompdf->setPaper('letter');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="pedido-'.$order->folio.'.pdf"',
        ]);
    }

    private function pdfImageSource(?string $path): ?string
    {
        if (blank($path)) return null;
        if (Str::startsWith($path, ['http://', 'https://'])) return $path;

        $absolutePath = realpath(public_path(ltrim($path, '/\\')));
        if (! $absolutePath || ! is_file($absolutePath)) return null;

        return 'file:///'.str_replace('\\', '/', $absolutePath);
    }

    private function form(Order $order): View
    {
        return view('admin.orders.form', [
            'order' => $order,
            'customers' => Customer::orderBy('name')->get(),
            'products' => CatalogProduct::active()
                ->with(['salePackages:id,catalog_product_id,name,quantity,unit_public_price,public_price,is_default,sort_order'])
                ->orderBy('name')
                ->get(['id', 'name', 'public_price']),
            // En pedidos administrativos se muestran todos los paquetes, incluso si no están publicados.
            'bundles' => CatalogBundle::with('items.product')->orderByDesc('is_active')->orderBy('name')->get(),
            'statuses' => Order::STATUSES,
        ]);
    }

    private function saveOrder(Order $order, Request $request): Order
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'required_without:new_customer_name', 'exists:customers,id'],
            'new_customer_name' => ['nullable', 'required_without:customer_id', 'string', 'max:160'],
            'new_customer_phone' => ['nullable', 'regex:/^\d{10}$/'],
            'new_customer_email' => ['nullable', 'email', 'max:160'],
            'new_customer_address' => ['nullable', 'string', 'max:1000'],
            'ordered_at' => ['required', 'date'],
            'delivery_at' => ['nullable', 'date', 'after_or_equal:ordered_at'],
            'delivery_time' => ['nullable', 'date_format:H:i'],
            'delivery_place' => ['nullable', 'string', 'max:1000'],
            'delivery_map_url' => ['nullable', 'string', 'max:6000'],
            'delivery_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'delivery_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', Rule::in(['product', 'bundle'])],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.sale_package_id' => ['nullable', 'integer', Rule::exists('catalog_product_sale_packages', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'reference_links' => ['nullable', 'array'],
            'reference_links.*' => ['nullable', 'url', 'max:1000'],
            'reference_files' => ['nullable', 'array'],
            'reference_files.*' => ['nullable', 'image', 'max:10240'],
            'remove_references' => ['nullable', 'array'],
            'remove_references.*' => ['integer'],
            'discount_type' => ['required', Rule::in(['fixed', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'has_shipping' => ['nullable', 'boolean'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'advance_payment' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'payment_received' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'observations' => ['nullable', 'string', 'max:3000'],
        ]);

        $customer = filled($data['customer_id'] ?? null)
            ? Customer::findOrFail($data['customer_id'])
            : Customer::create([
                'name' => $data['new_customer_name'], 'phone' => $data['new_customer_phone'] ?? null,
                'email' => $data['new_customer_email'] ?? null, 'address' => $data['new_customer_address'] ?? null,
            ]);

        $productIds = collect($data['items'])->where('item_type', 'product')->pluck('item_id');
        $bundleIds = collect($data['items'])->where('item_type', 'bundle')->pluck('item_id');
        $products = CatalogProduct::whereIn('id', $productIds)->get()->keyBy('id');
        $bundles = CatalogBundle::with('items.product')->whereIn('id', $bundleIds)->get()->keyBy('id');
        $salePackageIds = collect($data['items'])->where('item_type', 'product')->pluck('sale_package_id')->filter();
        $salePackages = CatalogProductSalePackage::whereIn('id', $salePackageIds)->get()->keyBy('id');
        $items = collect($data['items'])->map(function ($item) use ($products, $bundles, $salePackages) {
            $isBundle = $item['item_type'] === 'bundle';
            $record = $isBundle ? $bundles->get($item['item_id']) : $products->get($item['item_id']);
            abort_unless($record, 422, 'Uno de los productos o paquetes ya no está disponible.');
            $quantity = (int) $item['quantity'];
            $contents = $isBundle ? $record->items->map(fn ($bundleItem) => $bundleItem->quantity.' × '.($bundleItem->product?->name ?? 'Producto'))->join("\n") : null;
            $salePackage = null;

            if (! $isBundle && filled($item['sale_package_id'] ?? null)) {
                $salePackage = $salePackages->get((int) $item['sale_package_id']);
                abort_unless($salePackage && (int) $salePackage->catalog_product_id === (int) $record->id, 422, 'La presentación elegida no pertenece al producto seleccionado.');
            }

            $price = ceil((float) $item['unit_price']);

            return ['item_type' => $item['item_type'], 'catalog_product_id' => $isBundle ? null : $record->id,
                'catalog_bundle_id' => $isBundle ? $record->id : null, 'catalog_product_sale_package_id' => $salePackage?->id,
                'product_name' => $record->name, 'contents_snapshot' => $contents,
                'sale_package_name' => $salePackage?->name, 'sale_package_quantity' => $salePackage?->quantity,
                'quantity' => $quantity, 'unit_price' => $price, 'line_total' => ceil($quantity * $price)];
        });
        $subtotal = round($items->sum('line_total'), 2);
        $discountValue = round((float) ($data['discount_value'] ?? 0), 2);
        $discountAmount = $data['discount_type'] === 'percent' ? round($subtotal * min($discountValue, 100) / 100, 2) : min($discountValue, $subtotal);
        $shipping = $request->boolean('has_shipping') ? round((float) ($data['shipping_cost'] ?? 0), 2) : 0;
        $total = max(0, round($subtotal - $discountAmount + $shipping, 2));
        $paymentReceived = round((float) ($data['payment_received'] ?? 0), 2);
        $advance = min(round((float) ($data['advance_payment'] ?? 0), 2) + $paymentReceived, $total);
        $deliveryMapUrl = $this->deliveryMapUrl($data['delivery_map_url'] ?? null);
        [$deliveryLat, $deliveryLng] = $this->deliveryCoordinates($data, $deliveryMapUrl);

        $order->fill([
            'customer_id' => $customer->id, 'created_by' => $order->created_by ?: $request->user()->id,
            'ordered_at' => $data['ordered_at'], 'delivery_at' => $data['delivery_at'] ?? null,
            'delivery_time' => $data['delivery_time'] ?? null, 'delivery_place' => $data['delivery_place'] ?? null,
            'delivery_map_url' => $deliveryMapUrl, 'delivery_lat' => $deliveryLat, 'delivery_lng' => $deliveryLng,
            'status' => $data['status'],
            'discount_type' => $data['discount_type'], 'discount_value' => $discountValue, 'subtotal' => $subtotal,
            'discount_amount' => $discountAmount, 'has_shipping' => $request->boolean('has_shipping'), 'shipping_cost' => $shipping,
            'total' => $total, 'advance_payment' => $advance, 'balance_due' => round($total - $advance, 2), 'observations' => $data['observations'] ?? null,
        ]);
        if (! $order->exists) {
            $order->folio = 'PED-'.now()->format('Ymd').'-'.str_pad((string) ((Order::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);
        }
        $order->save();
        $order->items()->delete();
        $order->items()->createMany($items->all());
        $this->syncReferences($order, $request);

        return $order;
    }

    private function syncReferences(Order $order, Request $request): void
    {
        $removeIds = collect($request->input('remove_references', []))->map(fn ($id) => (int) $id)->filter();

        if ($removeIds->isNotEmpty()) {
            $order->references()->whereIn('id', $removeIds)->get()->each(function ($reference) {
                $this->deletePublicFile($reference->path);
                $reference->delete();
            });
        }

        $sortOrder = (int) ($order->references()->max('sort_order') ?? 0);

        foreach (array_filter($request->input('reference_links', [])) as $url) {
            $order->references()->create([
                'type' => 'link',
                'url' => trim((string) $url),
                'label' => parse_url((string) $url, PHP_URL_HOST) ?: 'Referencia',
                'sort_order' => ++$sortOrder,
            ]);
        }

        foreach ($request->file('reference_files', []) as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $order->references()->create([
                'type' => 'image',
                'path' => $this->storeReferenceImage($file, $order),
                'label' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'sort_order' => ++$sortOrder,
            ]);
        }
    }

    private function deliveryMapUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $value, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        }

        return $value;
    }

    private function deliveryCoordinates(array $data, ?string $deliveryMapUrl = null): array
    {
        $lat = $data['delivery_lat'] ?? null;
        $lng = $data['delivery_lng'] ?? null;

        if (is_numeric($lat) && is_numeric($lng)) {
            return [(float) $lat, (float) $lng];
        }

        $url = (string) ($deliveryMapUrl ?? $data['delivery_map_url'] ?? '');

        foreach ([
            '/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/',
            '/[?&]q=(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/',
            '/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/',
        ] as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return [(float) $matches[1], (float) $matches[2]];
            }
        }

        if (preg_match('/!2d(-?\d+(?:\.\d+)?)!3d(-?\d+(?:\.\d+)?)/', $url, $matches)) {
            return [(float) $matches[2], (float) $matches[1]];
        }

        return [null, null];
    }

    private function storeReferenceImage($file, Order $order): string
    {
        $directory = 'images/orders/'.$order->id.'/referencias';
        $absoluteDirectory = public_path($directory);

        if (! File::isDirectory($absoluteDirectory)) {
            File::makeDirectory($absoluteDirectory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.Str::uuid().'.'.$extension;
        $file->move($absoluteDirectory, $filename);

        return $directory.'/'.$filename;
    }

    private function deletePublicFile(?string $path): void
    {
        if (blank($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        $absolutePath = public_path(ltrim($path, '/\\'));

        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}
