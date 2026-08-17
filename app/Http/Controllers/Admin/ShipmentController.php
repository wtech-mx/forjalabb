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
            } catch(Throwable $e) { report($e); return redirect()->route('admin.shipments.show',$shipment)->withErrors(['skydropx'=>'El seguimiento se creó, pero la guía no pudo generarse: '.$e->getMessage()]); }
        }
        return redirect()->route('admin.shipments.show', $shipment)->with('status', 'Envío preparado. Ya puedes compartir el enlace de seguimiento.');
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

    public function generateGuide(Request $request, Shipment $shipment, SkydropxService $skydropx): RedirectResponse
    {
        abort_unless($shipment->method === 'skydropx', 422);
        abort_if(filled($shipment->skydropx_shipment_id), 409, 'Este envío ya tiene una guía generada.');
        $data=$request->validate(['rate_id'=>['required','string','max:160'],'carrier'=>['required','string','max:120'],'service'=>['required','string','max:160'],'price'=>['required','numeric','min:0']]);
        try {
            $response=$skydropx->createShipment($data['rate_id']); $created=data_get($response,'data.0',data_get($response,'data',$response));
            $shipment->update(['skydropx_rate_id'=>$data['rate_id'],'skydropx_shipment_id'=>$created['id']??null,'carrier'=>$data['carrier'],'quoted_service'=>$data['service'],'quoted_amount'=>$data['price'],'tracking_number'=>$created['master_tracking_number']??$created['tracking_number']??$created['tracking_code']??null,'tracking_url'=>$created['tracking_url']??null,'label_url'=>$created['label_url']??$created['label']??null,'status'=>'ready']);
            $shipment->events()->create(['phase'=>'carrier','title'=>'Guía de envío generada','description'=>$data['carrier'].' · '.$data['service'],'occurred_at'=>now(),'is_public'=>true]);
            return back()->with('status','Guía generada correctamente con Skydropx.');
        } catch(Throwable $e){report($e);return back()->withErrors(['skydropx'=>'No se pudo generar la guía: '.$e->getMessage()]);}
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
}
