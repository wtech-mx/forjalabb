@extends('layouts.app')

@section('title', 'Panel administrativo | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">

            @can('catalog.view')
            <div class="catalog-magazine-dashboard mb-4">
                <div class="catalog-magazine-dashboard-copy"><span><i class="bi bi-journal-richtext"></i> Catálogo compartible</span><h1>Abre tu catálogo animado.</h1><p>Elige si deseas mostrar precios antes de compartir el enlace con tus clientes.</p></div>
                <div class="catalog-magazine-dashboard-actions"><a href="{{ route('catalog.magazine.priced') }}" target="_blank"><i class="bi bi-tags-fill"></i><span><strong>Ver con precios</strong><small>Ideal para venta directa</small></span><i class="bi bi-arrow-up-right"></i></a><a href="{{ route('catalog.magazine.unpriced') }}" target="_blank"><i class="bi bi-eye-slash-fill"></i><span><strong>Ver sin precios</strong><small>Ideal para cotización</small></span><i class="bi bi-arrow-up-right"></i></a></div>
            </div>
            @endcan

            <div class="admin-header mb-3">
                <div><div class="eyebrow">Smart Tags</div><h2 class="h4 fw-bold mt-2 mb-0">Identificaciones registradas</h2></div>
                <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.tags.index') }}">Administrar tags</a>
            </div>
            <div class="row g-3 mb-4">
                @foreach ([
                    ['Smart Tags', $totalTags, 'bi-qr-code-scan'],
                    ['Tags activos', $activeTags, 'bi-broadcast-pin'],
                    ['Biker Tags', $bikerTags, 'bi-bicycle'],
                    ['Dog Tags', $dogTags, 'bi-heart-fill'],
                ] as [$label, $value, $icon])
                    <div class="col-6 col-lg-3"><div class="metric-card analytics-metric-card"><i class="bi {{ $icon }}"></i><span>{{ $label }}</span><strong>{{ $value }}</strong></div></div>
                @endforeach
            </div>

            <div class="panel-card mb-4">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                    <h2 class="h5 fw-bold mb-0">Ultimos productos</h2>
                    <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.catalog.index') }}">Ver catalogo</a>
                </div>
                @include('admin.catalog-products.partials.table', ['products' => $latestProducts])
            </div>

            <div class="admin-header mt-5 mb-3">
                <div>
                    <div class="eyebrow">Ultimos 30 dias</div>
                    <h2 class="fw-bold mt-2 mb-0">Metricas del sitio</h2>
                </div>
                <span class="badge text-bg-light"><i class="bi bi-shield-check me-1"></i>Analitica privada</span>
            </div>

            <div class="row g-3 mb-4">
                @foreach ([
                    ['Visitas', $pageViews, 'bi-eye-fill'],
                    ['Visitantes', $uniqueVisitors, 'bi-people-fill'],
                    ['Clics en productos', $productClicks, 'bi-bag-heart-fill'],
                    ['Clics a WhatsApp', $whatsappClicks, 'bi-whatsapp'],
                ] as [$label, $value, $icon])
                    <div class="col-6 col-lg-3">
                        <div class="metric-card analytics-metric-card">
                            <i class="bi {{ $icon }}"></i><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="panel-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3"><h3 class="h5 fw-bold mb-0">Visitas por dia</h3><small class="text-secondary">14 dias</small></div>
                @php($maxDaily = max(1, $dailyViews->max('total')))
                <div class="analytics-bars" aria-label="Grafica de visitas diarias">
                    @foreach ($dailyViews as $day)
                        <div><span style="height: {{ max(4, ($day['total'] / $maxDaily) * 100) }}%" title="{{ $day['total'] }} visitas"></span><small>{{ $day['label'] }}</small></div>
                    @endforeach
                </div>
            </div>

            <div class="row g-4 mb-4">
                @foreach ([
                    ['Paginas mas visitadas', $topPages, 'path'],
                    ['Secciones mas vistas', $topSections, 'label'],
                    ['Productos consultados', $topProducts, 'label'],
                ] as [$heading, $rows, $key])
                    <div class="col-lg-4"><div class="panel-card h-100"><h3 class="h6 fw-bold mb-3">{{ $heading }}</h3><div class="analytics-ranking">
                        @forelse ($rows as $row)
                            <div><span title="{{ $row->{$key} }}">{{ $row->{$key} }}</span><strong>{{ number_format($row->total) }}</strong></div>
                        @empty
                            <p class="text-secondary small mb-0">Aun no hay datos.</p>
                        @endforelse
                    </div></div></div>
                @endforeach
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-6"><div class="panel-card h-100"><h3 class="h6 fw-bold mb-3">Dispositivos</h3><div class="analytics-chips">
                    @forelse ($devices as $device)<span><i class="bi {{ $device->device === 'mobile' ? 'bi-phone' : ($device->device === 'tablet' ? 'bi-tablet' : 'bi-laptop') }}"></i>{{ ucfirst($device->device ?: 'desconocido') }} <b>{{ $device->total }}</b></span>@empty <small class="text-secondary">Sin datos</small> @endforelse
                </div></div></div>
                <div class="col-lg-6"><div class="panel-card h-100"><h3 class="h6 fw-bold mb-3">Origen de visitas</h3><div class="analytics-ranking">
                    @forelse ($sources as $source)<div><span>{{ $source->source }}</span><strong>{{ $source->total }}</strong></div>@empty <p class="text-secondary small mb-0">Sin datos</p> @endforelse
                </div></div></div>
            </div>

        </div>
    </section>
@endsection
