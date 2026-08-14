@extends('layouts.app')

@section('title', 'Panel administrativo | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Operacion</div>
                    <h1 class="fw-bold mt-2 mb-0">Panel administrativo</h1>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-dark" href="{{ route('admin.tags.create', ['type' => 'biker']) }}"><i class="bi bi-plus-circle me-2"></i>Biker Tag</a>
                    <a class="btn btn-outline-dark" href="{{ route('admin.tags.create', ['type' => 'dog']) }}"><i class="bi bi-plus-circle me-2"></i>Dog Tag</a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @foreach ([
                    ['label' => 'Productos', 'value' => $totalProducts],
                    ['label' => 'Activos', 'value' => $activeProducts],
                    ['label' => 'Usuarios', 'value' => $totalUsers],
                    ['label' => 'Roles', 'value' => $totalRoles],
                ] as $metric)
                    <div class="col-6 col-lg-3">
                        <div class="metric-card">
                            <span>{{ $metric['label'] }}</span>
                            <strong>{{ $metric['value'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

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

            <div class="panel-card mb-4">
                <div class="eyebrow">Accesos rapidos</div>
                <h2 class="h4 fw-bold mt-2 mb-3">¿Que quieres administrar?</h2>
                <div class="admin-access-grid">
                    <a href="{{ route('admin.tags.index') }}"><i class="bi bi-qr-code-scan"></i><span><strong>Smart Tags</strong><small>Crear y consultar Biker o Dog Tags</small></span><i class="bi bi-chevron-right"></i></a>
                    @can('catalog.view')
                        <a href="{{ route('admin.catalog.index') }}"><i class="bi bi-bag-heart-fill"></i><span><strong>Catalogo</strong><small>Productos, inventario y precios</small></span><i class="bi bi-chevron-right"></i></a>
                        <a href="{{ route('admin.packages.index') }}"><i class="bi bi-box-seam-fill"></i><span><strong>Paquetes</strong><small>Armar ofertas y combinaciones</small></span><i class="bi bi-chevron-right"></i></a>
                    @endcan
                    @can('users.view')<a href="{{ route('admin.users.index') }}"><i class="bi bi-people-fill"></i><span><strong>Usuarios</strong><small>Gestionar cuentas administrativas</small></span><i class="bi bi-chevron-right"></i></a>@endcan
                    @can('roles.view')<a href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock-fill"></i><span><strong>Roles</strong><small>Controlar permisos de acceso</small></span><i class="bi bi-chevron-right"></i></a>@endcan
                </div>
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
