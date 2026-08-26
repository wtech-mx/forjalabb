@extends('layouts.app')
@section('title', 'Reporte de ventas | ForjaLab')
@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">Ventas y gastos</div>
                <h1 class="fw-bold mt-2 mb-0">Reporte de ventas</h1>
                <p class="text-secondary mb-0 mt-2">{{ $start->format('d/m/Y') }} al {{ $end->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="panel-card mb-4">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-md-4">
                    <label class="form-label">Periodo</label>
                    <select class="form-select" name="mode">
                        <option value="month" @selected($mode === 'month')>Mes</option>
                        <option value="week" @selected($mode === 'week')>Semana</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de referencia</label>
                    <input class="form-control" type="date" name="date" value="{{ $date->format('Y-m-d') }}">
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-dark w-100"><i class="bi bi-funnel-fill me-2"></i>Ver reporte</button>
                </div>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Pedidos</small><h2 class="h3 fw-bold mb-0">{{ $summary['orders_count'] }}</h2></div></div>
            <div class="col-md-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Completados / pagados</small><h2 class="h3 fw-bold text-success mb-0">{{ $summary['completed_count'] }}</h2></div></div>
            <div class="col-md-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Aun deben</small><h2 class="h3 fw-bold text-danger mb-0">{{ $summary['pending_count'] }}</h2></div></div>
            <div class="col-md-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Saldo pendiente</small><h2 class="h3 fw-bold text-danger mb-0">${{ number_format($summary['total_pending'], 0) }}</h2></div></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Total vendido</small><h2 class="h3 fw-bold mb-0">${{ number_format($summary['total_sold'], 0) }}</h2></div></div>
            <div class="col-lg-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Total cobrado</small><h2 class="h3 fw-bold text-success mb-0">${{ number_format($summary['total_paid'], 0) }}</h2></div></div>
            <div class="col-lg-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Gastos estimados</small><h2 class="h3 fw-bold text-warning mb-0">${{ number_format($summary['total_expense'], 0) }}</h2></div></div>
            <div class="col-lg-3 col-6"><div class="panel-card h-100"><small class="text-secondary">Utilidad estimada</small><h2 class="h3 fw-bold mb-0">${{ number_format($summary['estimated_profit'], 0) }}</h2></div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="panel-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Gasto y utilidad por producto</h2>
                        <span class="badge text-bg-light">{{ $productRows->count() }} conceptos</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cant.</th>
                                    <th>Vendido</th>
                                    <th>Gasto</th>
                                    <th class="text-end">Utilidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productRows as $row)
                                    <tr>
                                        <td class="fw-bold">{{ $row['name'] }}</td>
                                        <td><span class="badge text-bg-{{ $row['type'] === 'Paquete' ? 'success' : 'warning' }}">{{ $row['type'] }}</span></td>
                                        <td>{{ $row['quantity'] }}</td>
                                        <td>${{ number_format($row['revenue'], 0) }}</td>
                                        <td>${{ number_format($row['expense'], 0) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($row['profit'], 0) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-secondary py-4">No hay productos vendidos en este periodo.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel-card h-100">
                    <h2 class="h5 fw-bold mb-3">Pedidos incluidos</h2>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Pedido</th><th>Cliente</th><th>Total</th><th>Saldo</th></tr></thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td><a class="fw-bold text-dark" href="{{ route('admin.orders.show', $order) }}">{{ $order->folio }}</a><small class="d-block text-secondary">{{ $order->ordered_at->format('d/m/Y') }}</small></td>
                                        <td>{{ $order->customer->name }}</td>
                                        <td>${{ number_format($order->total, 0) }}</td>
                                        <td class="fw-bold {{ $order->balance_due > 0 ? 'text-danger' : 'text-success' }}">${{ number_format($order->balance_due, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-secondary py-4">No hay pedidos en este periodo.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
