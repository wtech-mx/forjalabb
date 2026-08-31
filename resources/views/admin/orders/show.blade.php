@extends('layouts.app')
@section('title', $order->folio.' | ForjaLab')
@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <a class="order-back-link" href="{{ route('admin.orders.index') }}"><i class="bi bi-arrow-left"></i>Volver a pedidos</a>
                <div class="eyebrow mt-2">Pedido {{ $order->folio }}</div>
                <div class="d-flex align-items-center gap-3 flex-wrap"><h1 class="fw-bold mt-2 mb-0">{{ $order->customer->name }}</h1><span class="order-status order-status-{{ $order->status }} mt-2"><i class="bi bi-circle-fill"></i>{{ \App\Models\Order::STATUSES[$order->status] }}</span></div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-outline-dark" href="{{ route('admin.orders.pdf', $order) }}">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Descargar PDF
                </a>

                @if($order->has_shipping && in_array($order->status, ['in_progress', 'ready', 'delivered'], true))
                    @if($order->shipment)
                        <a class="btn btn-warning" href="{{ route('admin.shipments.show', $order->shipment) }}">
                            <i class="bi bi-truck me-2"></i>Gestionar envio
                        </a>
                    @else
                        @can('orders.manage')
                            <a class="btn btn-warning" href="{{ route('admin.shipments.create', $order) }}">
                                <i class="bi bi-truck me-2"></i>Preparar envio
                            </a>
                        @endcan
                    @endif
                @endif

                @can('orders.manage')
                    @if($order->archived_at)
                        <form method="POST" action="{{ route('admin.orders.restore', $order) }}">
                            @csrf
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Restaurar
                            </button>
                        </form>
                    @else
                        <a class="btn btn-dark" href="{{ route('admin.orders.edit', $order) }}">
                            <i class="bi bi-pencil me-2"></i>Editar
                        </a>
                        <form method="POST" action="{{ route('admin.orders.archive', $order) }}" data-confirm="Ya no aparecerá en la lista principal, pero podrás restaurarlo después." data-confirm-title="¿Archivar este pedido?" data-confirm-button="Sí, archivar">
                            @csrf
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-archive me-2"></i>Archivar
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>

        <div class="order-detail-metrics mb-4">
            <article><i class="bi bi-cash-stack"></i><div><small>Total del pedido</small><strong>${{ number_format($order->total, 2) }}</strong></div></article>
            <article><i class="bi bi-check2-circle"></i><div><small>Anticipo pagado</small><strong>${{ number_format($order->advance_payment, 2) }}</strong></div></article>
            <article class="{{ $order->balance_due > 0 ? 'is-due' : 'is-paid' }}"><i class="bi bi-wallet2"></i><div><small>Saldo pendiente</small><strong>${{ number_format($order->balance_due, 2) }}</strong></div></article>
            <article><i class="bi bi-calendar-event"></i><div><small>Entrega programada</small><strong>{{ $order->delivery_at?->format('d/m/Y') ?: 'Por definir' }}</strong></div></article>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="panel-card">
                    <div class="d-flex justify-content-between mb-3">
                        <h2 class="h5 fw-bold section-title-icon"><i class="bi bi-box-seam-fill"></i>Productos y paquetes</h2>
                        <span class="order-count-badge">{{ $order->items->sum('quantity') }} pieza(s)</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th class="text-end">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <span class="order-product-name"><i class="bi bi-box2-heart"></i><strong>{{ $item->product_name }}</strong></span>
                                            @if($item->item_type === 'bundle')
                                                <span class="badge text-bg-success ms-1">Paquete</span>
                                                <small class="d-block text-secondary mt-1" style="white-space:pre-line">Incluye: {{ $item->contents_snapshot }}</small>
                                            @elseif($item->sale_package_name)
                                                <span class="badge text-bg-warning ms-1">{{ $item->sale_package_name }}</span>
                                                <small class="d-block text-secondary mt-1">{{ $item->sale_package_quantity }} pieza{{ $item->sale_package_quantity === 1 ? '' : 's' }} · precio aplicado ${{ number_format($item->unit_price, 0) }} c/u</small>
                                            @endif
                                            @if($item->selected_colors)
                                                <small class="d-block text-secondary mt-1">Colores: {{ collect($item->selected_colors)->map(fn ($color, $index) => 'Pieza '.($index + 1).': '.\Illuminate\Support\Str::headline($color))->join(' · ') }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="order-totals ms-auto">
                        <div><span>Subtotal</span><strong>${{ number_format($order->subtotal, 2) }}</strong></div>
                        <div><span>Descuento</span><strong>-${{ number_format($order->discount_amount, 2) }}</strong></div>
                        <div><span>Envio</span><strong>${{ number_format($order->shipping_cost, 2) }}</strong></div>
                        <div class="total"><span>Total</span><strong>${{ number_format($order->total, 2) }}</strong></div>
                        <div><span>Anticipo pagado</span><strong>${{ number_format($order->advance_payment, 2) }}</strong></div>
                        <div class="balance"><span>Saldo pendiente</span><strong>${{ number_format($order->balance_due, 2) }}</strong></div>
                    </div>
                </div>

                @if($order->references->isNotEmpty())
                    <div class="panel-card mt-4">
                        <h2 class="h5 fw-bold section-title-icon"><i class="bi bi-images"></i>Referencias</h2>
                        <div class="order-reference-grid">
                            @foreach($order->references as $reference)
                                <a class="order-reference-card text-decoration-none text-dark" href="{{ $reference->display_url }}" target="_blank" rel="noopener">
                                    @if($reference->type === 'image')
                                        <img src="{{ $reference->display_url }}" alt="{{ $reference->label }}">
                                    @else
                                        <span><i class="bi bi-link-45deg"></i>{{ $reference->label ?: $reference->url }}</span>
                                    @endif
                                    <small>{{ $reference->type === 'image' ? 'Foto' : 'Link' }}</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="panel-card mb-4 order-info-card">
                    <h2 class="h5 fw-bold section-title-icon"><i class="bi bi-person-fill"></i>Cliente</h2>
                    <div class="order-customer-profile"><span>{{ strtoupper(mb_substr($order->customer->name, 0, 1)) }}</span><div><strong>{{ $order->customer->name }}</strong><small>Cliente del pedido</small></div></div>
                    <a class="order-info-row" href="tel:{{ $order->customer->phone }}"><i class="bi bi-telephone-fill"></i><span><small>Telefono</small>{{ $order->customer->phone ?: 'Sin telefono' }}</span></a>
                    <a class="order-info-row" href="mailto:{{ $order->customer->email }}"><i class="bi bi-envelope-fill"></i><span><small>Correo</small>{{ $order->customer->email ?: 'Sin correo' }}</span></a>
                    <div class="order-info-row"><i class="bi bi-house-door-fill"></i><span><small>Direccion</small>{{ $order->customer->address ?: 'Sin direccion' }}</span></div>
                </div>
                @can('orders.manage')
                    @php
                        $whatsappPhone = $order->customer->whatsapp ?: $order->customer->phone;
                        $whatsappMessage = "Hola {$order->customer->name}, te contactamos de ForjaLab respecto a tu pedido {$order->folio}.";
                    @endphp
                    <div class="panel-card mb-4 order-whatsapp-card">
                        <h2 class="h5 fw-bold section-title-icon"><i class="bi bi-whatsapp"></i>Enviar WhatsApp</h2>
                        <form method="POST" action="{{ route('admin.orders.whatsapp.send', $order) }}">@csrf
                            <label class="form-label fw-bold" for="whatsapp-phone">Numero</label><input class="form-control mb-3" id="whatsapp-phone" name="phone" value="{{ old('phone', $whatsappPhone) }}" placeholder="55 1234 5678" required>
                            <label class="form-label fw-bold" for="whatsapp-message">Mensaje</label><textarea class="form-control" id="whatsapp-message" name="message" rows="5" maxlength="4096" required>{{ old('message', $whatsappMessage) }}</textarea>
                            <button class="btn btn-success w-100 mt-3" type="submit"><i class="bi bi-send-fill me-2"></i>Enviar mensaje</button>
                        </form>
                    </div>
                @endcan
                <div class="panel-card order-info-card">
                    <h2 class="h5 fw-bold section-title-icon"><i class="bi bi-card-checklist"></i>Detalles</h2>
                    <p><strong>Pedido:</strong> {{ $order->ordered_at->format('d/m/Y') }}</p>
                    <p><strong>Entrega:</strong> {{ $order->delivery_at?->format('d/m/Y') ?: 'Por definir' }}{{ $order->delivery_time ? ' · '.\Illuminate\Support\Carbon::parse($order->delivery_time)->format('H:i') : '' }}</p>
                    <p><strong>Tipo de entrega:</strong><br>{{ \App\Models\Order::DELIVERY_METHODS[$order->delivery_method] ?? 'Por definir' }}</p>
                    @if($order->delivery_maps_link)
                        <p><strong>Maps:</strong><br><a href="{{ $order->delivery_maps_link }}" target="_blank" rel="noopener">Abrir ubicacion</a></p>
                    @endif
                    <p class="mb-0"><strong>Observaciones:</strong><br>{{ $order->observations ?: 'Sin observaciones' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
