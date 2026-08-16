@extends('layouts.app')
@section('title', 'Clientes y prospectos | ForjaLab')
@section('content')
<section class="admin-section"><div class="container">
    <div class="admin-header"><div><div class="eyebrow">Relación comercial</div><h1 class="fw-bold mt-2 mb-0">Clientes y prospectos</h1></div>@if($pendingCount)<span class="lead-pending-counter"><i class="bi bi-bell-fill"></i>{{ $pendingCount }} por contactar</span>@endif</div>
    <div class="lead-filter-bar panel-card mb-4"><form class="row g-2 align-items-end" method="GET"><div class="col-md"><label class="form-label">Buscar</label><input class="form-control" name="q" value="{{ $search }}" placeholder="Nombre, correo, teléfono o empresa"></div><div class="col-md-3"><label class="form-label">Tipo o estado</label><select class="form-select" name="status"><option value="">Todos</option><option value="prospects" @selected($status==='prospects')>Prospectos web activos</option>@foreach($statuses as $value=>$label)<option value="{{ $value }}" @selected($status===$value)>{{ $label }}</option>@endforeach</select></div><div class="col-md-auto"><button class="btn btn-dark w-100"><i class="bi bi-funnel-fill me-1"></i>Filtrar</button></div></form></div>
    <div class="customer-admin-grid">
    @forelse($customers as $customer)
        <article class="customer-admin-card {{ $customer->lead_source === 'website_popup' ? 'web-lead' : '' }}">
            <div class="customer-admin-head"><span class="customer-avatar">{{ Str::upper(Str::substr($customer->name,0,1)) }}</span><div><h2>{{ $customer->name }}</h2><small>{{ $customer->company ?: 'Persona particular' }}</small></div>@if($customer->lead_source === 'website_popup')<span class="badge text-bg-warning"><i class="bi bi-globe2 me-1"></i>Prospecto web</span>@else<span class="badge text-bg-light">Cliente</span>@endif</div>
            <div class="customer-contact-list"><a href="mailto:{{ $customer->email }}"><i class="bi bi-envelope-fill"></i>{{ $customer->email ?: 'Sin correo' }}</a><a href="tel:{{ $customer->phone }}"><i class="bi bi-telephone-fill"></i>{{ $customer->phone ?: 'Sin teléfono' }}</a>@if($customer->whatsapp)<a href="https://wa.me/{{ preg_replace('/\D+/','',$customer->whatsapp) }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i>{{ $customer->whatsapp }}</a>@endif</div>
            @if($customer->interested_service)<div class="customer-interest"><small>Servicio de interés</small><strong>{{ str($customer->interested_service)->replace('_',' ')->title() }}</strong></div>@endif
            <div class="customer-admin-meta"><span><i class="bi bi-receipt"></i>{{ $customer->orders_count }} pedidos</span><span><i class="bi bi-calendar3"></i>{{ $customer->created_at->format('d/m/Y H:i') }}</span></div>
            @can('customers.manage')<form class="customer-status-form" method="POST" action="{{ route('admin.customers.update',$customer) }}">@csrf @method('PUT')<select class="form-select form-select-sm" name="lead_status">@foreach($statuses as $value=>$label)<option value="{{ $value }}" @selected($customer->lead_status===$value)>{{ $label }}</option>@endforeach</select><button class="btn btn-sm btn-outline-dark">Guardar</button></form>@endcan
        </article>
    @empty<div class="panel-card text-center py-5"><i class="bi bi-people fs-1 text-secondary"></i><p class="mt-3 mb-0">No encontramos clientes con estos filtros.</p></div>@endforelse
    </div><div class="mt-4">{{ $customers->links() }}</div>
</div></section>
@endsection
