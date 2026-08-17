@extends('layouts.app')
@section('title','Envíos | ForjaLab')
@section('content')
<section class="admin-section"><div class="container">
<div class="admin-header"><div><div class="eyebrow">Operación</div><h1>Envíos y seguimiento</h1></div>@can('orders.manage')<a class="btn btn-dark" href="{{ route('admin.shipments.select-order') }}"><i class="bi bi-plus-lg me-2"></i>Nuevo envío</a>@endcan</div>
<div class="panel-card"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Pedido</th><th>Cliente</th><th>Método</th><th>Estado</th><th>Guía</th><th></th></tr></thead><tbody>
@forelse($shipments as $shipment)<tr><td>{{ $shipment->order->folio }}</td><td>{{ $shipment->order->customer->name }}</td><td>{{ \App\Models\Shipment::METHODS[$shipment->method] }}</td><td><span class="badge text-bg-dark">{{ \App\Models\Shipment::STATUSES[$shipment->status] }}</span></td><td>{{ $shipment->tracking_number ?: 'Pendiente' }}</td><td class="text-end"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.shipments.show',$shipment) }}">Gestionar</a></td></tr>
@empty<tr><td colspan="6" class="text-center py-5 text-secondary">Aún no hay envíos. Puedes crear uno desde aquí o desde un pedido en producción.</td></tr>@endforelse
</tbody></table></div>{{ $shipments->links() }}</div>
</div></section>
@endsection
