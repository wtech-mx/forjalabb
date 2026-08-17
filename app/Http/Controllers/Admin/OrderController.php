<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\CatalogBundle;
use App\Models\Customer;
use App\Models\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        return view('admin.orders.index', [
            'orders' => Order::with('customer')->when($search, function ($query) use ($search) {
                $query->where(fn ($q) => $q->where('folio', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")));
            })->latest()->paginate(15)->withQueryString(),
            'search' => $search,
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
        return view('admin.orders.show', ['order' => $order->load(['customer', 'items', 'creator', 'shipment.events.media'])]);
    }

    public function edit(Order $order): View
    {
        return $this->form($order->load('items'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        DB::transaction(fn () => $this->saveOrder($order, $request));

        return redirect()->route('admin.orders.show', $order)->with('status', 'Pedido actualizado correctamente.');
    }

    public function pdf(Order $order): Response
    {
        $order->load(['customer', 'items.product', 'items.bundle', 'creator']);
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
            'products' => CatalogProduct::active()->orderBy('name')->get(['id', 'name', 'public_price']),
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
            'new_customer_phone' => ['nullable', 'string', 'max:30'],
            'new_customer_email' => ['nullable', 'email', 'max:160'],
            'new_customer_address' => ['nullable', 'string', 'max:1000'],
            'ordered_at' => ['required', 'date'],
            'delivery_at' => ['nullable', 'date', 'after_or_equal:ordered_at'],
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', Rule::in(['product', 'bundle'])],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'discount_type' => ['required', Rule::in(['fixed', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'has_shipping' => ['nullable', 'boolean'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'advance_payment' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
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
        $items = collect($data['items'])->map(function ($item) use ($products, $bundles) {
            $isBundle = $item['item_type'] === 'bundle';
            $record = $isBundle ? $bundles->get($item['item_id']) : $products->get($item['item_id']);
            abort_unless($record, 422, 'Uno de los productos o paquetes ya no está disponible.');
            $quantity = (int) $item['quantity'];
            $price = round((float) $item['unit_price'], 2);
            $contents = $isBundle ? $record->items->map(fn ($bundleItem) => $bundleItem->quantity.' × '.($bundleItem->product?->name ?? 'Producto'))->join("\n") : null;
            return ['item_type' => $item['item_type'], 'catalog_product_id' => $isBundle ? null : $record->id,
                'catalog_bundle_id' => $isBundle ? $record->id : null, 'product_name' => $record->name,
                'contents_snapshot' => $contents, 'quantity' => $quantity, 'unit_price' => $price,
                'line_total' => round($quantity * $price, 2)];
        });
        $subtotal = round($items->sum('line_total'), 2);
        $discountValue = round((float) ($data['discount_value'] ?? 0), 2);
        $discountAmount = $data['discount_type'] === 'percent' ? round($subtotal * min($discountValue, 100) / 100, 2) : min($discountValue, $subtotal);
        $shipping = $request->boolean('has_shipping') ? round((float) ($data['shipping_cost'] ?? 0), 2) : 0;
        $total = max(0, round($subtotal - $discountAmount + $shipping, 2));
        $advance = min(round((float) ($data['advance_payment'] ?? 0), 2), $total);

        $order->fill([
            'customer_id' => $customer->id, 'created_by' => $order->created_by ?: $request->user()->id,
            'ordered_at' => $data['ordered_at'], 'delivery_at' => $data['delivery_at'] ?? null, 'status' => $data['status'],
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

        return $order;
    }
}
