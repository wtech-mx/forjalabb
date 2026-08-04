@extends('layouts.app')

@section('title', 'Vista previa | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Vista previa</div>
                    <h1 class="fw-bold mt-2 mb-0">{{ $product->name }}</h1>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-dark" href="{{ route('admin.catalog.index') }}"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                    @can('catalog.manage')
                        <a class="btn btn-dark" href="{{ route('admin.catalog.edit', $product) }}"><i class="bi bi-pencil me-2"></i>Editar</a>
                    @endcan
                </div>
            </div>

            <div class="catalog-section rounded-preview">
                <div class="container px-0">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-light">Precio publico: ${{ number_format((float) $product->public_price, 2) }}</span>
                        <span class="badge text-bg-light">Ganancia: ${{ number_format((float) $product->public_profit, 2) }}</span>
                        <span class="badge text-bg-light">Stock: {{ $product->stock }}</span>
                    </div>
                    @if ($product->salePackages->isNotEmpty())
                        <div class="sale-package-list mb-3">
                            @foreach ($product->salePackages as $package)
                                <div class="{{ $package->is_default ? 'active' : '' }}">
                                    <span>{{ $package->name }}</span>
                                    <strong>${{ number_format((float) $package->public_price, 2) }}</strong>
                                    <small>Familiar ${{ number_format((float) $package->family_price, 2) }} · Empaque ${{ number_format((float) $package->packaging_cost, 2) }}</small>
                                    <small>Costo total ${{ number_format((float) $package->total_cost, 2) }} · Ganancia publica ${{ number_format((float) $package->public_profit, 2) }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if ($product->is_featured)
                        @include('catalog.partials.product-card', ['product' => $product])
                    @else
                        <div class="catalog-grid">
                            @include('catalog.partials.product-card', ['product' => $product])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
