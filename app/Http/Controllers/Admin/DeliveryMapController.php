<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryMapController extends Controller
{
    public function __invoke(Request $request): View
    {
        $date = Carbon::parse($request->query('date', now()->toDateString()));
        $orders = Order::query()
            ->with('customer')
            ->whereNull('archived_at')
            ->where('status', '!=', 'cancelled')
            ->whereDate('delivery_at', $date->toDateString())
            ->orderByRaw('delivery_time is null')
            ->orderBy('delivery_time')
            ->orderBy('folio')
            ->get();

        $pins = $orders
            ->filter(fn ($order) => filled($order->delivery_lat) && filled($order->delivery_lng))
            ->map(fn ($order) => [
                'folio' => $order->folio,
                'customer' => $order->customer->name,
                'phone' => $order->customer->phone,
                'time' => $order->delivery_time ? Carbon::parse($order->delivery_time)->format('H:i') : 'Sin horario',
                'place' => $order->delivery_place ?: 'Sin direccion',
                'lat' => (float) $order->delivery_lat,
                'lng' => (float) $order->delivery_lng,
                'url' => route('admin.orders.show', $order),
                'maps_url' => $order->delivery_maps_link,
                'balance' => (float) $order->balance_due,
            ])
            ->values();

        return view('admin.deliveries.map', compact('date', 'orders', 'pins'));
    }
}
