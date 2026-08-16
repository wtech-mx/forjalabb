@extends('layouts.app')
@section('title', 'Pedidos | ForjaLab')
@section('content')
<section class="admin-section"><div class="container">
    <div class="admin-header"><div><div class="eyebrow">Ventas y producción</div><h1 class="fw-bold mt-2 mb-0">Pedidos</h1></div>
        @can('orders.manage')<a class="btn btn-dark" href="{{ route('admin.orders.create') }}"><i class="bi bi-plus-circle me-2"></i>Nuevo pedido</a>@endcan
    </div>
    <div class="panel-card mb-4"><form class="row g-2" method="GET"><div class="col-md"><input class="form-control" name="q" value="{{ $search }}" placeholder="Buscar por folio, cliente o teléfono"></div><div class="col-md-auto"><button class="btn btn-outline-dark w-100"><i class="bi bi-search me-1"></i>Buscar</button></div></form></div>
    <div class="panel-card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Folio</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th>Total</th><th>Saldo</th><th></th></tr></thead><tbody>
    @forelse($orders as $order)<tr><td class="fw-bold">{{ $order->folio }}</td><td>{{ $order->customer->name }}<small class="d-block text-secondary">{{ $order->customer->phone }}</small></td><td>{{ $order->ordered_at->format('d/m/Y') }}</td><td><span class="badge text-bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">{{ \App\Models\Order::STATUSES[$order->status] }}</span></td><td>${{ number_format($order->total, 2) }}</td><td class="fw-bold {{ $order->balance_due > 0 ? 'text-danger' : 'text-success' }}">${{ number_format($order->balance_due, 2) }}</td><td class="text-end"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.orders.show', $order) }}">Ver</a></td></tr>
    @empty<tr><td colspan="7" class="text-center py-5 text-secondary">Todavía no hay pedidos registrados.</td></tr>@endforelse
    </tbody></table></div><div class="mt-3">{{ $orders->links() }}</div></div>
</div></section>
@endsection
