@extends('layouts.app')
@section('title', 'Pedidos | ForjaLab')
@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">Ventas y produccion</div>
                <h1 class="fw-bold mt-2 mb-0">{{ $showArchived ? 'Pedidos archivados' : 'Pedidos' }}</h1>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-outline-dark" href="{{ route('admin.orders.index', $showArchived ? [] : ['archived' => 1]) }}">
                    <i class="bi bi-archive me-2"></i>{{ $showArchived ? 'Ver activos' : 'Archivados' }}
                </a>
                <a class="btn btn-outline-dark" href="{{ route('admin.deliveries.map', ['date' => $deliveryDate ?: now()->format('Y-m-d')]) }}">
                    <i class="bi bi-map-fill me-2"></i>Mapa
                </a>
                @can('orders.manage')
                    <a class="btn btn-dark" href="{{ route('admin.orders.create') }}"><i class="bi bi-plus-circle me-2"></i>Nuevo pedido</a>
                @endcan
            </div>
        </div>

        <div class="order-summary-grid mb-4">
            <article><i class="bi bi-receipt-cutoff"></i><div><small>{{ $showArchived ? 'Archivados' : 'Pedidos activos' }}</small><strong>{{ number_format($summary['total']) }}</strong></div></article>
            <article><i class="bi bi-gear-wide-connected"></i><div><small>En proceso</small><strong>{{ number_format($summary['production']) }}</strong></div></article>
            <article><i class="bi bi-bag-check-fill"></i><div><small>Listos para entregar</small><strong>{{ number_format($summary['ready']) }}</strong></div></article>
            <article class="is-balance"><i class="bi bi-wallet2"></i><div><small>Saldo por cobrar</small><strong>${{ number_format($summary['balance'], 2) }}</strong></div></article>
        </div>

        <div class="panel-card order-filter-card mb-4">
            <form class="row g-2 align-items-end" method="GET">
                @if($showArchived)
                    <input type="hidden" name="archived" value="1">
                @endif
                <div class="col-md">
                    <label class="form-label"><i class="bi bi-search me-1"></i>Buscar pedido</label>
                    <input class="form-control form-control-lg" name="q" value="{{ $search }}" placeholder="Folio, cliente o telefono">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha de entrega</label>
                    <input class="form-control form-control-lg" type="date" name="delivery_date" value="{{ $deliveryDate }}">
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-outline-dark w-100"><i class="bi bi-search me-1"></i>Buscar</button>
                </div>
                @if($search || $deliveryDate)
                    <div class="col-md-auto">
                        <a class="btn btn-outline-secondary w-100" href="{{ route('admin.orders.index', $showArchived ? ['archived' => 1] : []) }}">Limpiar</a>
                    </div>
                @endif
            </form>
        </div>

        <div class="panel-card order-list-panel p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 order-list-table">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Pedido</th>
                            <th>Entrega</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><a class="order-folio" href="{{ route('admin.orders.show', $order) }}"><i class="bi bi-receipt"></i>{{ $order->folio }}</a></td>
                                <td>
                                    <div class="order-customer"><span>{{ strtoupper(mb_substr($order->customer->name, 0, 1)) }}</span><div><strong>{{ $order->customer->name }}</strong><small><i class="bi bi-telephone me-1"></i>{{ $order->customer->phone ?: 'Sin telefono' }}</small></div></div>
                                </td>
                                <td data-label="Pedido"><span class="order-date"><i class="bi bi-calendar3"></i>{{ $order->ordered_at->format('d/m/Y') }}</span></td>
                                <td data-label="Entrega">
                                    @if($order->delivery_at)
                                        <strong>{{ $order->delivery_at->format('d/m/Y') }}</strong>
                                        @if($order->delivery_time)
                                            <small class="d-block text-secondary"><i class="bi bi-clock me-1"></i>{{ \Illuminate\Support\Carbon::parse($order->delivery_time)->format('H:i') }}</small>
                                        @else
                                            <small class="d-block text-secondary">Sin horario</small>
                                        @endif
                                        @if($order->delivery_place)
                                            <small class="d-block text-secondary" style="max-width:260px;white-space:normal;"><i class="bi bi-geo-alt me-1"></i>{{ $order->delivery_place }}</small>
                                        @else
                                            <small class="d-block text-secondary">Sin direccion</small>
                                        @endif
                                    @else
                                        <span class="order-muted-pill"><i class="bi bi-calendar-x"></i>Por definir</span>
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    @php($statusIcon = ['pending'=>'hourglass-split','in_progress'=>'gear-wide-connected','ready'=>'bag-check-fill','delivered'=>'check-circle-fill','cancelled'=>'x-circle-fill'][$order->status] ?? 'circle')
                                    <span class="order-status order-status-{{ $order->status }}"><i class="bi bi-{{ $statusIcon }}"></i>{{ \App\Models\Order::STATUSES[$order->status] }}</span>
                                    @if($order->archived_at)
                                        <small class="d-block text-secondary mt-1">Archivado {{ $order->archived_at->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td data-label="Total" class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                <td data-label="Saldo"><span class="order-balance {{ $order->balance_due > 0 ? 'pending' : 'paid' }}"><i class="bi bi-{{ $order->balance_due > 0 ? 'exclamation-circle' : 'check-circle' }}"></i>${{ number_format($order->balance_due, 2) }}</span></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a class="btn btn-sm btn-dark" href="{{ route('admin.orders.show', $order) }}"><i class="bi bi-eye me-1"></i>Ver</a>
                                        @can('orders.manage')
                                            @if($showArchived)
                                                <form method="POST" action="{{ route('admin.orders.restore', $order) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.orders.archive', $order) }}" onsubmit="return confirm('Archivar este pedido? Ya no aparecera en la lista principal.')">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-archive"></i></button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-secondary">{{ $showArchived ? 'No hay pedidos archivados.' : 'Todavia no hay pedidos registrados.' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $orders->links() }}</div>
        </div>
    </div>
</section>
@endsection
