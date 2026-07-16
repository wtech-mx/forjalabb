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
                    ['label' => 'Total', 'value' => $totalTags],
                    ['label' => 'Activos', 'value' => $activeTags],
                    ['label' => 'Biker Tags', 'value' => $bikerTags],
                    ['label' => 'Dog Tags', 'value' => $dogTags],
                ] as $metric)
                    <div class="col-6 col-lg-3">
                        <div class="metric-card">
                            <span>{{ $metric['label'] }}</span>
                            <strong>{{ $metric['value'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                    <h2 class="h5 fw-bold mb-0">Ultimos tags</h2>
                    <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.tags.index') }}">Ver todos</a>
                </div>
                @include('admin.tags.partials.table', ['tags' => $latestTags])
            </div>
        </div>
    </section>
@endsection
