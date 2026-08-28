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
                @if($date->isToday())
                    <div class="col-md-auto ms-md-auto">
                        <button class="btn btn-success w-100" type="button" id="share-location">
                            <i class="bi bi-broadcast-pin me-2"></i>Compartir mi ubicacion
                        </button>
                    </div>
                @endif
            </form>
            @if($date->isToday())
                <div class="location-sharing-status mt-3" id="location-status" role="status">
                    <span class="status-dot"></span>
                    <span>Ubicacion sin compartir. El navegador te pedira permiso al activarla.</span>
                </div>
            @endif
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
    .delivery-pin-label{padding:.2rem .45rem;color:#1b120b;font-weight:800;background:#fff8ec;border:1px solid rgba(32,22,14,.18);border-radius:.5rem;box-shadow:0 8px 20px rgba(32,22,14,.14)}
    .delivery-pin-label::before{display:none}
    .location-sharing-status{display:flex;align-items:center;gap:.55rem;color:#6f665f;font-size:.9rem}
    .status-dot{width:.65rem;height:.65rem;border-radius:50%;background:#adb5bd;flex:0 0 auto}
    .location-sharing-status.is-active{color:#157347;font-weight:700}.location-sharing-status.is-active .status-dot{background:#20c997;box-shadow:0 0 0 .25rem rgba(32,201,151,.16)}
    .location-sharing-status.is-error{color:#b02a37}.location-sharing-status.is-error .status-dot{background:#dc3545}
    .driver-pin{display:grid;place-items:center;width:2.15rem;height:2.15rem;border:3px solid #fff;border-radius:50%;color:#fff;box-shadow:0 4px 14px rgba(0,0,0,.3)}
    .driver-pin i{font-size:1rem}.driver-pin.is-inactive{filter:grayscale(1);opacity:.7}
    @media(max-width:767.98px){.delivery-map-canvas{height:480px;min-height:380px}.delivery-route-list{max-height:none}}
</style>
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pins = @json($pins);
        const locationsUrl = @json(route('admin.deliveries.locations', ['date' => $date->format('Y-m-d')]));
        const storeLocationUrl = @json(route('admin.deliveries.locations.store'));
        const isToday = @json($date->isToday());
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
            const marker = L.marker([pin.lat, pin.lng]).addTo(map).bindPopup(`
                <strong>${escapeHtml(pin.time)} · ${escapeHtml(pin.customer)}</strong><br>
                ${escapeHtml(pin.folio)}<br>
                ${escapeHtml(pin.place)}<br>
                ${pin.balance > 0 ? `<span style="color:#dc3545;font-weight:700">Debe $${Math.round(pin.balance).toLocaleString('es-MX')}</span><br>` : ''}
                <a href="${pin.url}">Ver pedido</a>
                ${pin.maps_url ? ` · <a href="${pin.maps_url}" target="_blank" rel="noopener">Maps</a>` : ''}
            `);
            marker.bindTooltip(`${pin.time} · ${pin.customer}`, {
                permanent: true,
                direction: 'top',
                offset: [0, -12],
                className: 'delivery-pin-label',
            });
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [35, 35] });
        }

        const driverLayers = new Map();
        const colors = ['#0d6efd', '#6f42c1', '#d63384', '#fd7e14', '#198754', '#0dcaf0'];
        const colorFor = (id) => colors[Number(id) % colors.length];

        const drawDrivers = (drivers) => {
            const visibleIds = new Set();
            drivers.forEach((driver) => {
                if (!driver.points.length) return;
                visibleIds.add(String(driver.user_id));
                const coordinates = driver.points.map((point) => [point.lat, point.lng]);
                const latest = driver.points[driver.points.length - 1];
                const color = colorFor(driver.user_id);
                const lastSeen = new Date(driver.last_seen).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const icon = L.divIcon({
                    className: '',
                    html: `<span class="driver-pin ${driver.active ? '' : 'is-inactive'}" style="background:${color}"><i class="bi bi-truck"></i></span>`,
                    iconSize: [35, 35], iconAnchor: [17, 17], popupAnchor: [0, -18],
                });
                let layer = driverLayers.get(String(driver.user_id));
                if (!layer) {
                    layer = {
                        route: L.polyline(coordinates, { color, weight: 4, opacity: .75 }).addTo(map),
                        marker: L.marker([latest.lat, latest.lng], { icon, zIndexOffset: 1000 }).addTo(map),
                    };
                    driverLayers.set(String(driver.user_id), layer);
                } else {
                    layer.route.setLatLngs(coordinates);
                    layer.marker.setLatLng([latest.lat, latest.lng]).setIcon(icon);
                }
                layer.marker.bindPopup(`<strong>${escapeHtml(driver.name)}</strong><br>${driver.active ? 'Compartiendo ahora' : `Ultima ubicacion: ${lastSeen}`}<br>Precision: ${latest.accuracy ? `${latest.accuracy} m` : 'no disponible'}`);
                layer.marker.bindTooltip(escapeHtml(driver.name), { direction: 'top', offset: [0, -15] });
            });
            driverLayers.forEach((layer, id) => {
                if (!visibleIds.has(id)) {
                    map.removeLayer(layer.route); map.removeLayer(layer.marker); driverLayers.delete(id);
                }
            });
        };

        const refreshDrivers = async () => {
            try {
                const response = await fetch(locationsUrl, { headers: { Accept: 'application/json' }, cache: 'no-store' });
                if (response.ok) drawDrivers((await response.json()).drivers || []);
            } catch (_) {
                // Se conserva la ultima posicion visible si falla temporalmente la red.
            }
        };
        refreshDrivers();
        window.setInterval(refreshDrivers, 5000);

        const shareButton = document.getElementById('share-location');
        const locationStatus = document.getElementById('location-status');
        let locationWatch = null;
        let lastSentAt = 0;

        const setLocationStatus = (message, state = '') => {
            if (!locationStatus) return;
            locationStatus.className = `location-sharing-status mt-3 ${state}`;
            locationStatus.querySelector('span:last-child').textContent = message;
        };

        const stopSharing = () => {
            if (locationWatch !== null) navigator.geolocation.clearWatch(locationWatch);
            locationWatch = null;
            shareButton.innerHTML = '<i class="bi bi-broadcast-pin me-2"></i>Compartir mi ubicacion';
            shareButton.classList.replace('btn-danger', 'btn-success');
            setLocationStatus('Ubicacion sin compartir. El navegador te pedira permiso al activarla.');
        };

        const sendPosition = async (position) => {
            if (Date.now() - lastSentAt < 4000) return;
            lastSentAt = Date.now();
            try {
                const response = await fetch(storeLocationUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json', 'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                    }),
                });
                if (!response.ok) throw new Error('No se pudo guardar la ubicacion');
                setLocationStatus(`Compartiendo en tiempo real · precision aproximada ${Math.round(position.coords.accuracy)} m`, 'is-active');
                refreshDrivers();
            } catch (_) {
                setLocationStatus('No se pudo enviar la ubicacion. Revisa tu conexion.', 'is-error');
            }
        };

        if (isToday && shareButton) shareButton.addEventListener('click', () => {
            if (locationWatch !== null) return stopSharing();
            if (!navigator.geolocation) return setLocationStatus('Este dispositivo no permite obtener la ubicacion.', 'is-error');
            setLocationStatus('Esperando permiso y señal GPS...');
            locationWatch = navigator.geolocation.watchPosition(sendPosition, (error) => {
                const messages = {
                    1: 'Permiso de ubicacion rechazado. Habilitalo en el navegador para compartir.',
                    2: 'No se pudo determinar tu ubicacion.',
                    3: 'El GPS tardo demasiado. Intenta de nuevo.',
                };
                setLocationStatus(messages[error.code] || 'No se pudo acceder a la ubicacion.', 'is-error');
                stopSharing();
                setLocationStatus(messages[error.code] || 'No se pudo acceder a la ubicacion.', 'is-error');
            }, { enableHighAccuracy: true, maximumAge: 3000, timeout: 15000 });
            shareButton.innerHTML = '<i class="bi bi-stop-circle me-2"></i>Dejar de compartir';
            shareButton.classList.replace('btn-success', 'btn-danger');
        });
    });
</script>
@endpush
@endsection
