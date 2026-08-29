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

        <div class="row g-4 mb-4 report-chart-grid">
            <div class="col-xl-8">
                <div class="panel-card h-100 report-chart-card">
                    <div class="report-chart-heading"><div><div class="eyebrow">Comportamiento del periodo</div><h2 class="h5 fw-bold mb-0">Ventas, gastos y utilidad</h2></div><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="report-chart-main"><canvas id="sales-timeline-chart" aria-label="Grafica de ventas, gastos y utilidad"></canvas></div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="panel-card h-100 report-chart-card">
                    <div class="report-chart-heading"><div><div class="eyebrow">Estado de cobro</div><h2 class="h5 fw-bold mb-0">Cobrado y pendiente</h2></div><i class="bi bi-pie-chart-fill"></i></div>
                    <div class="report-chart-doughnut"><canvas id="payments-chart" aria-label="Grafica de pagos cobrados y pendientes"></canvas></div>
                    <div class="report-payment-legend"><span><i class="paid"></i>Cobrado <strong>${{ number_format($summary['total_paid'], 0) }}</strong></span><span><i class="pending"></i>Pendiente <strong>${{ number_format($summary['total_pending'], 0) }}</strong></span></div>
                </div>
            </div>
            <div class="col-12">
                <div class="panel-card report-chart-card">
                    <div class="report-chart-heading"><div><div class="eyebrow">Productos destacados</div><h2 class="h5 fw-bold mb-0">Los que generan mayores ventas</h2></div><i class="bi bi-bar-chart-fill"></i></div>
                    <div class="report-chart-products"><canvas id="products-chart" aria-label="Grafica de ventas y utilidad por producto"></canvas></div>
                </div>
            </div>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const data = @json($chartData);
    const money = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 });
    const grid = 'rgba(90,53,29,.09)';
    const tooltip = { callbacks: { label: context => `${context.dataset.label}: ${money.format(context.raw)}` } };

    new Chart(document.getElementById('sales-timeline-chart'), {
        type: 'line',
        data: { labels: data.daily.map(x => x.label), datasets: [
            { label: 'Ventas', data: data.daily.map(x => x.sales), borderColor: '#ed741d', backgroundColor: 'rgba(237,116,29,.12)', fill: true, tension: .35, pointRadius: 3 },
            { label: 'Gastos', data: data.daily.map(x => x.expense), borderColor: '#b94b55', backgroundColor: 'transparent', tension: .35, pointRadius: 2 },
            { label: 'Utilidad', data: data.daily.map(x => x.profit), borderColor: '#47763b', backgroundColor: 'transparent', tension: .35, pointRadius: 2 },
        ]},
        options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { tooltip }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: grid }, ticks: { callback: value => money.format(value) } } } }
    });

    new Chart(document.getElementById('payments-chart'), {
        type: 'doughnut', data: { labels: ['Cobrado', 'Pendiente'], datasets: [{ data: [data.payments.paid, data.payments.pending], backgroundColor: ['#47763b', '#d45151'], borderWidth: 0, hoverOffset: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false }, tooltip } }
    });

    new Chart(document.getElementById('products-chart'), {
        type: 'bar', data: { labels: data.products.map(x => x.name), datasets: [
            { label: 'Vendido', data: data.products.map(x => x.revenue), backgroundColor: '#ed741d', borderRadius: 6 },
            { label: 'Utilidad', data: data.products.map(x => x.profit), backgroundColor: '#5f873f', borderRadius: 6 },
        ]}, options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { tooltip }, scales: { x: { beginAtZero: true, grid: { color: grid }, ticks: { callback: value => money.format(value) } }, y: { grid: { display: false } } } }
    });
});
</script>
@endpush
