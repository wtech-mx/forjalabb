@extends('layouts.app')
@section('title','Seguimiento '.$shipment->order->folio.' | ForjaLab')
@section('meta_description','Consulta el avance y las evidencias de tu pedido ForjaLab.')
@section('content')
@php
    $order = $shipment->order;
    $steps = [
        'pending' => ['label' => 'Pedido recibido', 'icon' => 'receipt-cutoff'],
        'in_progress' => ['label' => 'En producción', 'icon' => 'tools'],
        'ready' => ['label' => 'Listo para entregar', 'icon' => 'box-seam-fill'],
        'delivered' => ['label' => 'Entregado', 'icon' => 'check-circle-fill'],
    ];
    $stepKeys = array_keys($steps);
    $currentStatus = $order->status === 'cancelled' ? 'pending' : $order->status;
    $foundStep = array_search($currentStatus, $stepKeys, true);
    $currentStep = $foundStep === false ? 0 : $foundStep;
@endphp
<section class="order-tracking-page"><div class="container">
    <header class="tracking-hero">
        <div class="tracking-brand"><img src="{{ asset('icon-192.png') }}" alt="ForjaLab"><span>FORJALAB</span></div>
        <div class="row align-items-center g-4"><div class="col-lg-7"><span class="eyebrow"><i class="bi bi-activity me-1"></i> Seguimiento de pedido</span><h1>Hola, {{ str($order->customer->name)->before(' ') }}</h1><p>Aquí puedes ver claramente en qué etapa está tu pedido y consultar las fotografías de su proceso.</p></div><div class="col-lg-5"><div class="tracking-current"><span><i class="bi bi-{{ $steps[$currentStatus]['icon'] ?? 'activity' }}"></i></span><div><small>Estado actual</small><strong>{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</strong><b>Pedido {{ $order->folio }}</b></div></div></div></div>
    </header>
    @if($order->status === 'cancelled')<div class="alert alert-danger mt-4"><i class="bi bi-exclamation-triangle-fill me-2"></i>Este pedido aparece como cancelado. Contáctanos si necesitas ayuda.</div>@else
    <div class="tracking-progress-card"><div class="tracking-steps">@foreach($steps as $key => $step)@php $index = array_search($key, $stepKeys, true); @endphp<div class="tracking-step {{ $index < $currentStep ? 'is-complete' : '' }} {{ $index === $currentStep ? 'is-current' : '' }}"><div class="tracking-step-icon"><i class="bi bi-{{ $index < $currentStep ? 'check-lg' : $step['icon'] }}"></i></div><strong>{{ $step['label'] }}</strong><small>{{ $index < $currentStep ? 'Completado' : ($index === $currentStep ? 'Etapa actual' : 'Siguiente etapa') }}</small></div>@endforeach</div></div>
    @endif
    <div class="row g-4 mt-1"><div class="col-lg-8"><section class="tracking-panel"><div class="tracking-panel-heading"><span><i class="bi bi-images"></i></span><div><small>AVANCES REALES</small><h2>Así va tu pedido</h2></div></div><div class="tracking-timeline">
        @forelse($shipment->events as $event)<article class="tracking-event"><div class="tracking-event-marker"><i class="bi bi-check-lg"></i></div><div class="tracking-event-body"><time><i class="bi bi-calendar3 me-1"></i>{{ $event->occurred_at->format('d/m/Y · H:i') }}</time><h3>{{ $event->title }}</h3>@if($event->description)<p>{{ $event->description }}</p>@endif @if($event->media->isNotEmpty())<div class="tracking-gallery">@foreach($event->media as $media)@if($media->media_type === 'video')<video controls preload="metadata" src="{{ $media->url }}"></video>@else<a href="{{ $media->url }}" target="_blank"><img loading="lazy" src="{{ $media->url }}" alt="{{ $event->title }}"></a>@endif @endforeach</div>@endif</div></article>
        @empty<div class="tracking-empty"><i class="bi bi-camera"></i><h3>Pronto verás el primer avance</h3><p>Cuando nuestro equipo publique una fotografía del proceso aparecerá aquí.</p></div>@endforelse
    </div></section></div>
    <aside class="col-lg-4"><section class="tracking-panel tracking-order-card"><div class="tracking-panel-heading"><span><i class="bi bi-bag-check-fill"></i></span><div><small>RESUMEN</small><h2>Tu pedido</h2></div></div><div class="tracking-products">@foreach($order->items as $item)<div><span>{{ $item->quantity }}</span><p><strong>{{ $item->product_name }}</strong>@if(!empty($item->selected_colors))<small>Colores: {{ collect($item->selected_colors)->map(fn($color) => str($color)->headline())->join(', ') }}</small>@endif</p></div>@endforeach</div><hr><dl class="tracking-details"><div><dt><i class="bi bi-calendar-event"></i> Entrega estimada</dt><dd>{{ $order->delivery_at?->format('d/m/Y') ?: 'Por confirmar' }}{{ $order->delivery_time ? ' · '.$order->delivery_time->format('H:i').' h' : '' }}</dd></div><div><dt><i class="bi bi-truck"></i> Tipo de entrega</dt><dd>{{ \App\Models\Order::DELIVERY_METHODS[$order->delivery_method] ?? \App\Models\Shipment::METHODS[$shipment->method] ?? 'Por definir' }}</dd></div>@if($order->delivery_place)<div><dt><i class="bi bi-geo-alt-fill"></i> Lugar</dt><dd>{{ $order->delivery_place }}</dd></div>@endif</dl>@if($shipment->tracking_number)<div class="tracking-guide"><i class="bi bi-upc-scan"></i><div><small>Número de guía</small><strong>{{ $shipment->tracking_number }}</strong></div></div>@if($shipment->tracking_url)<a class="btn btn-dark w-100 mt-2" href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-2"></i>Rastrear con paquetería</a>@endif @endif</section>
        <div class="tracking-help"><i class="bi bi-whatsapp"></i><div><strong>¿Tienes alguna duda?</strong><p>Escríbenos y menciona tu folio {{ $order->folio }}.</p></div><a href="https://wa.me/525564442949?text={{ rawurlencode('Hola, tengo una duda sobre mi pedido '.$order->folio) }}" target="_blank" rel="noopener">Contactar</a></div>
    </aside></div>
</div></section>
@endsection
