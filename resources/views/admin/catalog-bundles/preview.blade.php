@extends('layouts.app')

@section('title', 'Vista previa paquete | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Vista previa</div>
                    <h1 class="fw-bold mt-2 mb-0">{{ $bundle->name }}</h1>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-dark" href="{{ route('admin.packages.index') }}"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                    @can('catalog.manage')
                        <a class="btn btn-dark" href="{{ route('admin.packages.edit', $bundle) }}"><i class="bi bi-pencil me-2"></i>Editar</a>
                    @endcan
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="panel-card">
                        @if ($bundle->image_url)
                            <img class="bundle-preview-cover" src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}">
                        @endif
                        <p class="mt-3 mb-0">{{ $bundle->description }}</p>
                        @if ($bundle->photos->isNotEmpty())
                            <div class="bundle-gallery mt-3">
                                @foreach ($bundle->photos as $photo)
                                    <img src="{{ $photo->image_url }}" alt="{{ $bundle->name }}">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="panel-card">
                        <div class="price-summary">
                            <div><span>Productos</span><strong>${{ number_format((float) $bundle->items_cost, 2) }}</strong></div>
                            <div><span>Empaque del paquete</span><strong>${{ number_format((float) $bundle->packaging_cost, 2) }}</strong></div>
                            <div><span>Costo total</span><strong>${{ number_format((float) $bundle->total_cost, 2) }}</strong></div>
                            <div><span>Familiar {{ $bundle->family_multiplier }}x</span><strong>${{ number_format((float) $bundle->family_price, 0) }}</strong><small>Ganancia ${{ number_format((float) $bundle->family_profit, 0) }}</small></div>
                            <div><span>Publico {{ $bundle->public_multiplier }}x</span><strong>${{ number_format((float) $bundle->public_price, 0) }}</strong><small>Ganancia ${{ number_format((float) $bundle->public_profit, 0) }}</small></div>
                        </div>
                    </div>
                    <div class="panel-card mt-4">
                        <h2 class="h5 fw-bold mb-3">Productos incluidos</h2>
                        <div class="sale-package-list">
                            @foreach ($bundle->items as $item)
                                <div>
                                    <span>{{ $item->product?->name }}</span>
                                    <strong>x{{ $item->quantity }}</strong>
                                    <small>Costo unitario ${{ number_format((float) $item->unit_cost, 2) }} · total ${{ number_format((float) $item->total_cost, 2) }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
