<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryLocation;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

    public function storeLocation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $location = DeliveryLocation::create([
            'user_id' => $request->user()->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => isset($data['accuracy']) ? round($data['accuracy']) : null,
            'recorded_at' => now(),
        ]);

        return response()->json(['recorded_at' => $location->recorded_at->toIso8601String()]);
    }

    public function locations(Request $request): JsonResponse
    {
        $date = Carbon::parse($request->query('date', now()->toDateString()));
        $locations = DeliveryLocation::query()
            ->with('user:id,name')
            ->whereBetween('recorded_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->orderBy('recorded_at')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($points) => [
                'user_id' => $points->first()->user_id,
                'name' => $points->first()->user->name,
                'active' => $points->last()->recorded_at->gt(now()->subSeconds(30)),
                'last_seen' => $points->last()->recorded_at->toIso8601String(),
                'points' => $points->map(fn ($point) => [
                    'lat' => $point->latitude,
                    'lng' => $point->longitude,
                    'accuracy' => $point->accuracy,
                    'recorded_at' => $point->recorded_at->toIso8601String(),
                ])->values(),
            ])->values();

        return response()->json(['drivers' => $locations]);
    }
}
