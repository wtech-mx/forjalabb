<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\SkydropxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ShipmentController extends Controller
{
    public function index(): View
    {
        return view('admin.shipments.index', ['shipments' => Shipment::with('order.customer')->latest()->paginate(20)]);
    }

    public function selectOrder(): View
    {
        return view('admin.shipments.select-order', [
            'orders' => Order::with('customer')
                ->where('has_shipping', true)
                ->whereIn('status', ['in_progress', 'ready', 'delivered'])
                ->whereDoesntHave('shipment')
                ->latest('ordered_at')
                ->paginate(20),
        ]);
    }

    public function quickQuoteView(SkydropxService $skydropx): View
    {
        $apiError = null;
        $guides = collect();
        $pickups = collect();
        $balance = null;

        try {
            $shipmentsResponse = $skydropx->shipments();
            $guides = $this->normalizeSkydropxShipments($shipmentsResponse);
            $balanceResponse = $skydropx->balance();
            $balance = [
                'amount' => (float) data_get($balanceResponse, 'data.balance', 0),
                'currency' => data_get($balanceResponse, 'data.currency', 'MXN'),
            ];
            $pickups = $this->normalizePickups($skydropx->pickups());
        } catch (Throwable $e) {
            report($e);
            $apiError = 'No se pudo sincronizar Skydropx: '.$e->getMessage();
        }

        return view('admin.shipments.quick-quote', compact('guides', 'pickups', 'balance', 'apiError'));
    }

    public function pickupCoverage(Request $request, SkydropxService $skydropx): JsonResponse
    {
        $data = $request->validate(['shipment_id' => ['required', 'uuid']]);
        try {
            return response()->json($skydropx->pickupCoverage($data['shipment_id']));
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => 'No se pudo consultar la cobertura: '.$e->getMessage()], 422);
        }
    }

    public function schedulePickup(Request $request, SkydropxService $skydropx): RedirectResponse
    {
        $data = $request->validate([
            'reference_shipment_id' => ['required', 'uuid'],
            'packages' => ['required', 'integer', 'min:1', 'max:100'],
            'total_weight' => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'scheduled_from' => ['required', 'date', 'after:now'],
            'scheduled_to' => ['required', 'date', 'after:scheduled_from'],
        ]);

        try {
            $skydropx->schedulePickup([
                'reference_shipment_id' => $data['reference_shipment_id'],
                'packages' => (int) $data['packages'],
                'total_weight' => (float) $data['total_weight'],
                'scheduled_from' => Carbon::parse($data['scheduled_from'])->format('Y-m-d H:i:s'),
                'scheduled_to' => Carbon::parse($data['scheduled_to'])->format('Y-m-d H:i:s'),
            ]);

            return redirect()->route('admin.shipments.quick-quote', ['tab' => 'pickups'])->with('status', 'Recoleccion solicitada correctamente a Skydropx.');
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['pickup' => 'No se pudo solicitar la recoleccion: '.$e->getMessage()]);
        }
    }

    public function quickQuote(Request $request, SkydropxService $skydropx): JsonResponse
    {
        $data = $request->validate([
            'destination_postal_code'=>['required','string','size:5'], 'destination_state'=>['required','string','max:120'],
            'destination_city'=>['required','string','max:120'], 'destination_neighborhood'=>['required','string','max:160'],
            'parcel_weight'=>['required','numeric','min:0.01','max:999'], 'parcel_length'=>['required','integer','min:1','max:999'],
            'parcel_width'=>['required','integer','min:1','max:999'], 'parcel_height'=>['required','integer','min:1','max:999'],
        ]);
        try {
            $response=$skydropx->quote($this->quotationPayload($data));
            return response()->json(['rates'=>$skydropx->rates($response),'quoted_at'=>now()->format('d/m/Y H:i')]);
        } catch(Throwable $e) { report($e); return response()->json(['message'=>'Skydropx no pudo cotizar: '.$e->getMessage()],422); }
    }

    public function create(Order $order): View
    {
        $this->ensureEligible($order);
        $shipment = new Shipment;
        $shipment->setRelation('order', $order->loadMissing('customer'));
        return view('admin.shipments.form', compact('order', 'shipment'));
    }

    public function store(Request $request, Order $order, SkydropxService $skydropx): RedirectResponse
    {
        $this->ensureEligible($order);
        abort_if($order->shipment()->exists(), 409, 'Este pedido ya tiene un envío.');
        $data = $this->shipmentData($request);
        $shipment = $order->shipment()->create($data + ['public_token' => Str::random(48), 'status' => 'preparing']);
        $shipment->events()->create(['phase' => 'production', 'title' => 'Pedido en producción', 'description' => 'Comenzamos a preparar tu pedido.', 'occurred_at' => now(), 'is_public' => true]);
        if ($shipment->method === 'skydropx' && filled($request->input('rate_id'))) {
            $rate=$request->validate(['rate_id'=>['required','string','max:160'],'rate_carrier'=>['required','string','max:120'],'rate_service'=>['required','string','max:160'],'rate_price'=>['required','numeric','min:0']]);
            try {
                $response=$skydropx->createShipment($rate['rate_id']); $created=data_get($response,'data.0',data_get($response,'data',$response));
                $shipment->update(['skydropx_rate_id'=>$rate['rate_id'],'skydropx_shipment_id'=>$created['id']??null,'carrier'=>$rate['rate_carrier'],'quoted_service'=>$rate['rate_service'],'quoted_amount'=>$rate['rate_price'],'tracking_number'=>$created['master_tracking_number']??$created['tracking_number']??$created['tracking_code']??null,'tracking_url'=>$created['tracking_url']??null,'label_url'=>$created['label_url']??$created['label']??null,'status'=>'ready']);
                $shipment->events()->create(['phase'=>'carrier','title'=>'Guía de envío generada','description'=>$rate['rate_carrier'].' · '.$rate['rate_service'],'occurred_at'=>now(),'is_public'=>true]);
            } catch(Throwable $e) {
                report($e);
                $route = $request->input('return_to') === 'order_edit' ? 'admin.orders.edit' : 'admin.shipments.show';
                $parameter = $route === 'admin.orders.edit' ? $order : $shipment;

                return redirect()->route($route, $parameter)->withErrors(['skydropx'=>'El seguimiento se creó, pero la guía no pudo generarse: '.$e->getMessage()]);
            }
        }
        $route = $request->input('return_to') === 'order_edit' ? 'admin.orders.edit' : 'admin.shipments.show';
        $parameter = $route === 'admin.orders.edit' ? $order : $shipment;

        return redirect()->route($route, $parameter)->with('status', 'Envío preparado. Ya puedes compartir el enlace de seguimiento.');
    }

    public function postalCode(string $postalCode, SkydropxService $skydropx): JsonResponse
    {
        abort_unless(preg_match('/^\d{5}$/', $postalCode), 422, 'Código postal inválido.');
        try { return response()->json(['places'=>$skydropx->postalCode($postalCode)]); }
        catch (Throwable $e) { report($e); return response()->json(['message'=>'No pudimos consultar ese código postal. Puedes llenar la dirección manualmente.'], 422); }
    }

    public function draftQuote(Request $request, Order $order, SkydropxService $skydropx): JsonResponse
    {
        $this->ensureEligible($order);
        $data = $this->shipmentData($request);
        foreach (['destination_postal_code','destination_state','destination_city','destination_neighborhood','parcel_weight','parcel_length','parcel_width','parcel_height'] as $field) abort_if(blank($data[$field] ?? null), 422, 'Completa destino, peso y dimensiones.');
        try {
            $response = $skydropx->quote($this->quotationPayload($data));
            return response()->json(['rates'=>$skydropx->rates($response),'quotation_id'=>data_get($response,'data.id',data_get($response,'id'))]);
        } catch (Throwable $e) { report($e); return response()->json(['message'=>'Skydropx no pudo cotizar: '.$e->getMessage()], 422); }
    }

    public function quoteRates(Shipment $shipment, SkydropxService $skydropx): JsonResponse
    {
        abort_unless($shipment->method === 'skydropx', 422, 'El método debe ser Skydropx.');
        foreach (['destination_postal_code', 'destination_state', 'destination_city', 'destination_neighborhood', 'parcel_weight', 'parcel_length', 'parcel_width', 'parcel_height'] as $field) {
            abort_if(blank($shipment->{$field}), 422, 'Completa y guarda el destino, peso y dimensiones antes de cotizar.');
        }

        try {
            $response = $skydropx->quote([
                'address_from' => ['country_code' => 'MX', 'postal_code' => config('services.skydropx.origin_postal_code'), 'area_level1' => config('services.skydropx.origin_state'), 'area_level2' => config('services.skydropx.origin_city'), 'area_level3' => config('services.skydropx.origin_neighborhood')],
                'address_to' => ['country_code' => 'MX', 'postal_code' => $shipment->destination_postal_code, 'area_level1' => $shipment->destination_state, 'area_level2' => $shipment->destination_city, 'area_level3' => $shipment->destination_neighborhood],
                'parcels' => [['length' => (int) $shipment->parcel_length, 'width' => (int) $shipment->parcel_width, 'height' => (int) $shipment->parcel_height, 'weight' => (float) $shipment->parcel_weight]],
            ]);
            $shipment->update(['quote_response' => $response]);

            return response()->json(['rates' => $skydropx->rates($response)]);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Skydropx no pudo cotizar: '.$e->getMessage()], 422);
        }
    }

    public function availableGuides(Order $order, SkydropxService $skydropx): JsonResponse
    {
        $this->ensureEligible($order);

        try {
            $guides = $this->normalizeSkydropxShipments($skydropx->shipments());
            $assignments = Shipment::query()
                ->with('order:id,folio')
                ->whereIn('skydropx_shipment_id', $guides->pluck('id'))
                ->get()
                ->keyBy('skydropx_shipment_id');
            $guides = $guides->map(function (array $guide) use ($assignments) {
                $assignment = $assignments->get($guide['id']);
                $guide['assigned'] = (bool) $assignment;
                $guide['assigned_order'] = $assignment?->order?->folio;

                return $guide;
            })
                ->sortBy(fn (array $guide) => ($guide['assigned'] ? '1' : '0').'|'.($guide['created_at'] ?? ''))
                ->values();

            return response()->json(['guides' => $guides]);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'No se pudieron consultar las guías de Skydropx: '.$e->getMessage()], 422);
        }
    }

    public function assignGuide(Request $request, Order $order, SkydropxService $skydropx): JsonResponse
    {
        $this->ensureEligible($order);
        $data = $request->validate(['guide_id' => ['required', 'string', 'max:160']]);

        if (Shipment::where('skydropx_shipment_id', $data['guide_id'])->where('order_id', '!=', $order->id)->exists()) {
            return response()->json(['message' => 'Esta guía ya está vinculada con otro pedido.'], 422);
        }

        try {
            $guide = $this->normalizeSkydropxShipments($skydropx->shipments())->firstWhere('id', $data['guide_id']);
            if (! $guide) return response()->json(['message' => 'La guía ya no está disponible en Skydropx.'], 404);

            $shipment = $order->shipment ?: $order->shipment()->create([
                'public_token' => Str::random(48),
                'method' => 'skydropx',
                'status' => 'preparing',
                'destination_address' => $order->customer?->address,
            ]);
            $shipment->update([
                'method' => 'skydropx',
                'status' => $this->localStatusForGuide($guide['status']),
                'skydropx_shipment_id' => $guide['id'],
                'carrier' => $guide['carrier'],
                'tracking_number' => $guide['tracking_number'],
                'tracking_url' => $guide['tracking_url'],
                'label_url' => $guide['label_url'],
                'quoted_amount' => $guide['total'],
                'quoted_service' => $guide['service'],
            ]);
            $shipment->events()->create([
                'phase' => 'carrier',
                'title' => 'Guía existente vinculada',
                'description' => $guide['carrier'].($guide['tracking_number'] ? ' · '.$guide['tracking_number'] : ''),
                'occurred_at' => now(),
                'is_public' => true,
            ]);

            return response()->json(['message' => 'La guía quedó vinculada al pedido '.$order->folio.'.']);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'No se pudo vincular la guía: '.$e->getMessage()], 422);
        }
    }

    public function generateGuide(Request $request, Shipment $shipment, SkydropxService $skydropx): JsonResponse|RedirectResponse
    {
        abort_unless($shipment->method === 'skydropx', 422);
        abort_if(filled($shipment->skydropx_shipment_id), 409, 'Este envío ya tiene una guía generada.');
        $data=$request->validate(['rate_id'=>['required','string','max:160'],'carrier'=>['required','string','max:120'],'service'=>['required','string','max:160'],'price'=>['required','numeric','min:0']]);
        try {
            $response=$skydropx->createShipment($data['rate_id']); $created=data_get($response,'data.0',data_get($response,'data',$response));
            $shipment->update(['skydropx_rate_id'=>$data['rate_id'],'skydropx_shipment_id'=>$created['id']??null,'carrier'=>$data['carrier'],'quoted_service'=>$data['service'],'quoted_amount'=>$data['price'],'tracking_number'=>$created['master_tracking_number']??$created['tracking_number']??$created['tracking_code']??null,'tracking_url'=>$created['tracking_url']??null,'label_url'=>$created['label_url']??$created['label']??null,'status'=>'ready']);
            $shipment->events()->create(['phase'=>'carrier','title'=>'Guía de envío generada','description'=>$data['carrier'].' · '.$data['service'],'occurred_at'=>now(),'is_public'=>true]);
            if ($request->expectsJson()) return response()->json(['message' => 'Guía generada correctamente con Skydropx.']);

            return back()->with('status','Guía generada correctamente con Skydropx.');
        } catch(Throwable $e){report($e);if ($request->expectsJson()) return response()->json(['message' => 'No se pudo generar la guía: '.$e->getMessage()], 422);return back()->withErrors(['skydropx'=>'No se pudo generar la guía: '.$e->getMessage()]);}
    }

    public function show(Shipment $shipment): View
    {
        return view('admin.shipments.show', ['shipment' => $shipment->load(['order.customer', 'order.items', 'events.media'])]);
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $data = $this->shipmentData($request);
        $data['status'] = $request->validate(['status' => ['required', Rule::in(array_keys(Shipment::STATUSES))]])['status'];
        if ($data['status'] === 'in_transit' && ! $shipment->shipped_at) $data['shipped_at'] = now();
        if ($data['status'] === 'delivered' && ! $shipment->delivered_at) $data['delivered_at'] = now();
        $shipment->update($data);
        return back()->with('status', 'Datos del envío actualizados.');
    }

    public function addEvent(Request $request, Shipment $shipment): RedirectResponse
    {
        $data = $request->validate([
            'phase' => ['required', Rule::in(['production','packing','carrier','in_transit','delivered','incident'])],
            'title' => ['required','string','max:160'], 'description' => ['nullable','string','max:2000'],
            'occurred_at' => ['required','date'], 'is_public' => ['nullable','boolean'],
            'evidence' => ['nullable','array','max:8'], 'evidence.*' => ['file','mimes:jpg,jpeg,png,webp,mp4,mov,webm','max:30720'],
        ]);
        $event = $shipment->events()->create([
            'phase' => $data['phase'], 'title' => $data['title'], 'description' => $data['description'] ?? null,
            'occurred_at' => $data['occurred_at'], 'is_public' => $request->boolean('is_public'),
        ]);
        $directory = public_path('images/shipping/'.$shipment->public_token);
        File::ensureDirectoryExists($directory);
        foreach ($request->file('evidence', []) as $file) {
            $name = Str::uuid().'.'.strtolower($file->getClientOriginalExtension());
            $mediaType = Str::startsWith((string) $file->getMimeType(), 'video/') ? 'video' : 'image';
            $originalName = $file->getClientOriginalName();
            $file->move($directory, $name);
            $event->media()->create(['media_type' => $mediaType, 'file_path' => 'images/shipping/'.$shipment->public_token.'/'.$name, 'original_name' => $originalName]);
        }
        return back()->with('status', 'Avance y evidencias agregados.');
    }

    public function quote(Request $request, Shipment $shipment, SkydropxService $skydropx): RedirectResponse
    {
        abort_unless($shipment->method === 'skydropx', 422, 'El método debe ser Skydropx.');
        $data = $request->validate([
            'origin_postal_code'=>['required','string','max:10'], 'origin_state'=>['required','string','max:120'],
            'origin_city'=>['required','string','max:120'], 'origin_neighborhood'=>['required','string','max:160'],
        ]);
        foreach (['destination_postal_code','destination_state','destination_city','destination_neighborhood','parcel_weight','parcel_length','parcel_width','parcel_height'] as $field) {
            if (blank($shipment->{$field})) return back()->withErrors(['skydropx' => 'Completa destino, peso y dimensiones antes de cotizar.']);
        }
        try {
            $response = $skydropx->quote([
                'address_from'=>['country_code'=>'MX','postal_code'=>$data['origin_postal_code'],'area_level1'=>$data['origin_state'],'area_level2'=>$data['origin_city'],'area_level3'=>$data['origin_neighborhood']],
                'address_to'=>['country_code'=>'MX','postal_code'=>$shipment->destination_postal_code,'area_level1'=>$shipment->destination_state,'area_level2'=>$shipment->destination_city,'area_level3'=>$shipment->destination_neighborhood],
                'parcels'=>[['length'=>(int)$shipment->parcel_length,'width'=>(int)$shipment->parcel_width,'height'=>(int)$shipment->parcel_height,'weight'=>(float)$shipment->parcel_weight]],
            ]);
            $shipment->update(['quote_response' => $response]);
            return back()->with('status', 'Cotización recibida de Skydropx.');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['skydropx' => 'Skydropx no pudo cotizar: '.$e->getMessage()]);
        }
    }

    private function shipmentData(Request $request): array
    {
        return $request->validate([
            'method'=>['required',Rule::in(array_keys(Shipment::METHODS))], 'carrier'=>['nullable','string','max:120'],
            'tracking_number'=>['nullable','string','max:160'], 'tracking_url'=>['nullable','url','max:2000'],
            'destination_postal_code'=>['nullable','string','max:10'], 'destination_state'=>['nullable','string','max:120'],
            'destination_city'=>['nullable','string','max:120'], 'destination_neighborhood'=>['nullable','string','max:160'],
            'destination_address'=>['nullable','string','max:1000'], 'cod_amount'=>['nullable','numeric','min:0'],
            'quoted_amount'=>['nullable','numeric','min:0'], 'quoted_service'=>['nullable','string','max:160'],
            'parcel_weight'=>['nullable','numeric','min:0.01'], 'parcel_length'=>['nullable','integer','min:1'],
            'parcel_width'=>['nullable','integer','min:1'], 'parcel_height'=>['nullable','integer','min:1'],
        ]);
    }

    private function quotationPayload(array $data): array
    {
        return ['address_from'=>['country_code'=>'MX','postal_code'=>config('services.skydropx.origin_postal_code'),'area_level1'=>config('services.skydropx.origin_state'),'area_level2'=>config('services.skydropx.origin_city'),'area_level3'=>config('services.skydropx.origin_neighborhood')],
            'address_to'=>['country_code'=>'MX','postal_code'=>$data['destination_postal_code'],'area_level1'=>$data['destination_state'],'area_level2'=>$data['destination_city'],'area_level3'=>$data['destination_neighborhood']],
            'parcels'=>[['length'=>(int)$data['parcel_length'],'width'=>(int)$data['parcel_width'],'height'=>(int)$data['parcel_height'],'weight'=>(float)$data['parcel_weight']]]];
    }

    private function ensureEligible(Order $order): void
    {
        abort_unless($order->has_shipping, 422, 'El pedido no tiene envío habilitado.');
        abort_unless(in_array($order->status, ['in_progress','ready','delivered'], true), 422, 'El envío se habilita cuando el pedido entra a producción.');
    }

    private function normalizeSkydropxShipments(array $response)
    {
        $included = collect($response['included'] ?? []);

        return collect($response['data'] ?? [])->map(function (array $shipment) use ($included) {
            $attributes = $shipment['attributes'] ?? $shipment;
            $related = $included->filter(fn ($item) => data_get($item, 'relationships.shipment.data.id') === ($shipment['id'] ?? null));
            $label = $related->first(fn ($item) =>
                str_contains(strtolower((string) ($item['type'] ?? '')), 'label')
                || filled(data_get($item, 'attributes.label_url'))
                || filled(data_get($item, 'attributes.tracking_number'))
            );
            $labelAttributes = $label['attributes'] ?? [];
            $address = $related->first(fn ($item) => in_array(strtolower((string) ($item['type'] ?? '')), ['address', 'addresses'], true) && data_get($item, 'attributes.address_type') === 'to');
            $status = strtolower((string) ($attributes['workflow_status'] ?? $attributes['status'] ?? 'created'));
            $tracking = $attributes['master_tracking_number'] ?? $labelAttributes['tracking_number'] ?? null;
            $carrier = $attributes['carrier_name'] ?? data_get($attributes, 'rate.provider_display_name') ?? 'Paqueteria';

            return [
                'id' => $shipment['id'] ?? $attributes['id'] ?? '',
                'carrier' => $carrier,
                'service' => $attributes['service_name'] ?? data_get($attributes, 'rate.provider_service_name') ?? data_get($attributes, 'rate.service_name') ?? null,
                'status' => $status,
                'active' => ! in_array($status, ['delivered', 'cancelled', 'canceled', 'error'], true),
                'tracking_number' => $tracking,
                'tracking_url' => $attributes['tracking_url'] ?? $labelAttributes['tracking_url_provider'] ?? $this->carrierTrackingUrl($carrier, $tracking),
                'label_url' => $attributes['label_url'] ?? $labelAttributes['label_url'] ?? null,
                'recipient' => data_get($address, 'attributes.name'),
                'created_at' => $attributes['created_at'] ?? null,
                'total' => (float) ($attributes['total'] ?? 0),
            ];
        })->filter(fn ($shipment) => $shipment['active'])->values();
    }

    private function normalizePickups(array $response)
    {
        return collect($response['data'] ?? [])->map(function (array $pickup) {
            $attributes = $pickup['attributes'] ?? $pickup;
            return [
                'id' => $pickup['id'] ?? $attributes['id'] ?? '',
                'status' => $attributes['status'] ?? 'pending',
                'request_number' => $attributes['request_number'] ?? null,
                'packages' => $attributes['packages'] ?? 0,
                'weight' => $attributes['total_weight'] ?? 0,
                'from' => $attributes['scheduled_from'] ?? null,
                'to' => $attributes['scheduled_to'] ?? null,
                'error' => $attributes['error_reason'] ?? null,
            ];
        });
    }

    private function carrierTrackingUrl(string $carrier, ?string $tracking): ?string
    {
        if (! $tracking) return null;
        $name = strtolower($carrier);
        return match (true) {
            str_contains($name, 'dhl') => 'https://www.dhl.com/mx-es/home/rastreo.html?tracking-id='.urlencode($tracking),
            str_contains($name, 'fedex') => 'https://www.fedex.com/fedextrack/?trknbr='.urlencode($tracking),
            str_contains($name, 'estafeta') => 'https://www.estafeta.com/Herramientas/Rastreo?guias='.urlencode($tracking),
            str_contains($name, 'ups') => 'https://www.ups.com/track?tracknum='.urlencode($tracking),
            str_contains($name, 'paquetexpress') => 'https://www.paquetexpress.com.mx/rastreo/'.urlencode($tracking),
            default => null,
        };
    }

    private function localStatusForGuide(string $status): string
    {
        return match ($status) {
            'delivered' => 'delivered',
            'in_transit', 'shipped', 'out_for_delivery' => 'in_transit',
            'exception', 'error', 'cancelled', 'canceled' => 'exception',
            default => 'ready',
        };
    }
}
