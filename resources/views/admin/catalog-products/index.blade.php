@extends('layouts.app')

@section('title', 'Catalogo | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Mini catalogo</div>
                    <h1 class="fw-bold mt-2 mb-0">Productos</h1>
                </div>
                @can('catalog.manage')
                    <a class="btn btn-dark" href="{{ route('admin.catalog.create') }}">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo producto
                    </a>
                @endcan
            </div>

            <div class="panel-card">
                @include('admin.catalog-products.partials.table', ['products' => $products])
                <div class="mt-3">{{ $products->links() }}</div>
            </div>
        </div>
    </section>
@endsection
