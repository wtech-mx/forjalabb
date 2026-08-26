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

        <div class="panel-card mb-4">
            <form class="row g-2 align-items-end" method="GET">
                @if($showArchived)
                    <input type="hidden" name="archived" value="1">
                @endif
                <div class="col-md">
                    <label class="form-label">Buscar</label>
                    <input class="form-control" name="q" value="{{ $search }}" placeholder="Buscar por folio, cliente o telefono">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha de entrega</label>
                    <input class="form-control" type="date" name="delivery_date" value="{{ $deliveryDate }}">
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

        <div class="panel-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
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
                                <td class="fw-bold">{{ $order->folio }}</td>
                                <td>
                                    {{ $order->customer->name }}
                                    <small class="d-block text-secondary">{{ $order->customer->phone }}</small>
                                </td>
                                <td>{{ $order->ordered_at->format('d/m/Y') }}</td>
                                <td>
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
                                        <span class="text-secondary">Por definir</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge text-bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">{{ \App\Models\Order::STATUSES[$order->status] }}</span>
                                    @if($order->archived_at)
                                        <small class="d-block text-secondary mt-1">Archivado {{ $order->archived_at->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td class="fw-bold {{ $order->balance_due > 0 ? 'text-danger' : 'text-success' }}">${{ number_format($order->balance_due, 2) }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.orders.show', $order) }}">Ver</a>
                                        @can('orders.manage')
                                            @if($showArchived)
                                                <form method="POST" action="{{ route('admin.orders.restore', $order) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success" type="submit">Restaurar</button>
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
            <div class="mt-3">{{ $orders->links() }}</div>
        </div>
    </div>
</section>
@endsection
