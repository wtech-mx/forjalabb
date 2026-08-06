@extends('layouts.app')

@section('title', 'Paquetes | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Catalogo</div>
                    <h1 class="fw-bold mt-2 mb-0">Paquetes</h1>
                </div>
                @can('catalog.manage')
                    <a class="btn btn-dark" href="{{ route('admin.packages.create') }}">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo paquete
                    </a>
                @endcan
            </div>

            <div class="panel-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Paquete</th>
                                <th>Productos</th>
                                <th>Costo</th>
                                <th>Publico</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bundles as $bundle)
                                <tr>
                                    <td>
                                        <strong>{{ $bundle->name }}</strong>
                                        <div class="text-muted small">{{ $bundle->slug }}</div>
                                    </td>
                                    <td>{{ $bundle->items_count }}</td>
                                    <td>${{ number_format((float) $bundle->total_cost, 2) }}</td>
                                    <td>
                                        <strong>${{ number_format((float) $bundle->public_price, 0) }}</strong>
                                        <div class="text-muted small">Gan. ${{ number_format((float) $bundle->public_profit, 0) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $bundle->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $bundle->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.packages.preview', $bundle) }}"><i class="bi bi-eye"></i></a>
                                            @can('catalog.manage')
                                                <a class="btn btn-sm btn-dark" href="{{ route('admin.packages.edit', $bundle) }}"><i class="bi bi-pencil"></i></a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">Todavia no hay paquetes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $bundles->links() }}</div>
            </div>
        </div>
    </section>
@endsection
