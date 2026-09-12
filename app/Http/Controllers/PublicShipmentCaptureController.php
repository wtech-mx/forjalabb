<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicShipmentCaptureController extends Controller
{
    private const STAGES = [
        'production' => ['title' => 'Pedido en producción', 'description' => 'Estamos trabajando en la preparación de tu pedido.'],
        'ready' => ['title' => 'Pedido listo para entregar', 'description' => 'Tu pedido quedó terminado y listo para su entrega.'],
        'delivered' => ['title' => 'Pedido entregado', 'description' => 'Tu pedido fue entregado correctamente.'],
    ];

    public function show(string $token): View
    {
        $shipment = $this->shipment($token);

        return view('shipments.capture', ['shipment' => $shipment, 'stages' => self::STAGES]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $shipment = $this->shipment($token);
        $data = $request->validate([
            'stage' => ['required', Rule::in(array_keys(self::STAGES))],
            'description' => ['nullable', 'string', 'max:1000'],
            'evidence' => ['required', 'array', 'min:1', 'max:6'],
            'evidence.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
        ]);

        $stage = self::STAGES[$data['stage']];
        $event = $shipment->events()->create([
            'phase' => $data['stage'] === 'ready' ? 'packing' : $data['stage'],
            'title' => $stage['title'],
            'description' => filled($data['description'] ?? null) ? $data['description'] : $stage['description'],
            'occurred_at' => now(),
            'is_public' => true,
        ]);

        $directory = public_path('images/shipping/'.$shipment->public_token);
        File::ensureDirectoryExists($directory);
        foreach ($request->file('evidence') as $file) {
            $name = Str::uuid().'.'.strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $file->move($directory, $name);
            $event->media()->create([
                'media_type' => 'image',
                'file_path' => 'images/shipping/'.$shipment->public_token.'/'.$name,
                'original_name' => $originalName,
            ]);
        }

        return back()->with('status', 'Las fotos se publicaron correctamente en el seguimiento del cliente.');
    }

    private function shipment(string $token): Shipment
    {
        return Shipment::where('capture_token', $token)->with('order.customer')->firstOrFail();
    }
}
