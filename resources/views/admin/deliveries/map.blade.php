@extends('layouts.app')
@section('title', 'Mapa de entregas | ForjaLab')
@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">Rutas y horarios</div>
                <h1 class="fw-bold mt-2 mb-0">Mapa de entregas</h1>
                <p class="text-secondary mb-0 mt-2">{{ $orders->count() }} entrega{{ $orders->count() === 1 ? '' : 's' }} para {{ $date->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="panel-card mb-4">
            <form class="row g-2 align-items-end" method="GET">
                <div class="col-md-4">
                    <label class="form-label">Dia de entrega</label>
                    <input class="form-control" type="date" name="date" value="{{ $date->format('Y-m-d') }}">
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-dark w-100"><i class="bi bi-geo-alt-fill me-2"></i>Ver ruta</button>
                </div>
                <div class="col-md-auto">
                    <a class="btn btn-outline-dark w-100" href="{{ route('admin.orders.index', ['delivery_date' => $date->format('Y-m-d')]) }}">Ver tabla</a>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="panel-card p-0 overflow-hidden">
                    <div id="delivery-map" class="delivery-map-canvas"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel-card delivery-route-list">
                    <h2 class="h5 fw-bold mb-3">Entregas del dia</h2>
                    @forelse($orders as $order)
                        <article class="delivery-route-item {{ filled($order->delivery_lat) && filled($order->delivery_lng) ? '' : 'missing-pin' }}">
                            <div>
                                <strong>{{ $order->delivery_time ? \Illuminate\Support\Carbon::parse($order->delivery_time)->format('H:i') : 'Sin horario' }}</strong>
                                <span>{{ $order->customer->name }}</span>
                                <small>{{ $order->folio }} · {{ $order->delivery_place ?: 'Sin direccion' }}</small>
                                @if($order->balance_due > 0)
                                    <small class="text-danger fw-bold">Debe ${{ number_format($order->balance_due, 0) }}</small>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @if($order->delivery_maps_link)
                                    <a class="btn btn-sm btn-outline-dark" href="{{ $order->delivery_maps_link }}" target="_blank" rel="noopener"><i class="bi bi-map"></i></a>
                                @endif
                                <a class="btn btn-sm btn-dark" href="{{ route('admin.orders.show', $order) }}">Ver</a>
                            </div>
                        </article>
                    @empty
                        <p class="text-secondary mb-0">No hay entregas para este dia.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .delivery-map-canvas{height:min(68vh,680px);min-height:460px;background:#f6f1e8}
    .delivery-route-list{display:grid;gap:.75rem;max-height:680px;overflow:auto}
    .delivery-route-item{display:flex;justify-content:space-between;gap:1rem;padding:.85rem;border:1px solid rgba(32,22,14,.1);border-radius:.85rem;background:#fff}
    .delivery-route-item>div:first-child{display:grid;gap:.15rem}
    .delivery-route-item strong{font-size:1.05rem}
    .delivery-route-item small{color:#6f665f}
    .delivery-route-item.missing-pin{border-style:dashed;background:#fff8ef}
    @media(max-width:767.98px){.delivery-map-canvas{height:480px;min-height:380px}.delivery-route-list{max-height:none}}
</style>
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pins = @json($pins);
        const map = L.map('delivery-map');
        const center = pins[0] ? [pins[0].lat, pins[0].lng] : [19.4326, -99.1332];
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[char]));

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        map.setView(center, pins.length ? 12 : 11);

        const bounds = [];
        pins.forEach((pin) => {
            bounds.push([pin.lat, pin.lng]);
            L.marker([pin.lat, pin.lng]).addTo(map).bindPopup(`
                <strong>${escapeHtml(pin.time)} · ${escapeHtml(pin.customer)}</strong><br>
                ${escapeHtml(pin.folio)}<br>
                ${escapeHtml(pin.place)}<br>
                ${pin.balance > 0 ? `<span style="color:#dc3545;font-weight:700">Debe $${Math.round(pin.balance).toLocaleString('es-MX')}</span><br>` : ''}
                <a href="${pin.url}">Ver pedido</a>
                ${pin.maps_url ? ` · <a href="${pin.maps_url}" target="_blank" rel="noopener">Maps</a>` : ''}
            `);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [35, 35] });
        }
    });
</script>
@endpush
@endsection
