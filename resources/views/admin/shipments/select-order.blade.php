@extends('layouts.app')
@section('title','Nuevo envío | ForjaLab')
@section('content')
<section class="admin-section"><div class="container">
<div class="admin-header"><div><div class="eyebrow">Nuevo envío</div><h1>Selecciona el pedido</h1><p class="text-secondary mb-0">Pedidos en producción o listos para entregar que tienen envío habilitado.</p></div><a class="btn btn-outline-dark" href="{{ route('admin.shipments.index') }}">Volver</a></div>
<div class="panel-card"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Pedido</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th>Destino registrado</th><th></th></tr></thead><tbody>
@forelse($orders as $order)<tr><td><strong>{{ $order->folio }}</strong></td><td>{{ $order->customer->name }}<small class="d-block text-secondary">{{ $order->customer->phone }}</small></td><td>{{ $order->ordered_at->format('d/m/Y') }}</td><td><span class="badge text-bg-dark">{{ \App\Models\Order::STATUSES[$order->status] }}</span></td><td>{{ $order->customer->address ?: 'Por capturar' }}</td><td class="text-end"><a class="btn btn-sm btn-dark" href="{{ route('admin.shipments.create',$order) }}">Vincular y preparar</a></td></tr>
@empty<tr><td colspan="6" class="text-center py-5"><i class="bi bi-check-circle fs-2 text-success"></i><p class="mt-2 mb-0">No hay pedidos disponibles para crear un envío.</p><small class="text-secondary">Activa “lleva envío” y coloca el pedido en producción.</small></td></tr>@endforelse
</tbody></table></div>{{ $orders->links() }}</div>
</div></section>
@endsection
