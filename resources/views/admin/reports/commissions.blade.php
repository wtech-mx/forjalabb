@extends('layouts.app')

@section('title', 'Comisiones | ForjaLab')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">Reportes</div>
                <h1 class="fw-bold mt-2 mb-0">Comisiones</h1>
                <p class="text-secondary mb-0 mt-2">{{ $start->format('d/m/Y') }} al {{ $end->format('d/m/Y') }} · {{ $seller->name }}</p>
            </div>
            <a class="btn btn-dark" href="{{ route('admin.reports.commissions.pdf', request()->query()) }}">
                <i class="bi bi-filetype-pdf me-2"></i>Exportar PDF
            </a>
        </div>

        <div class="panel-card mb-4">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label" for="start_date">Desde</label>
                    <input class="form-control" id="start_date" name="start_date" type="date" value="{{ $start->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="end_date">Hasta</label>
                    <input class="form-control" id="end_date" name="end_date" type="date" value="{{ $end->format('Y-m-d') }}">
                </div>
                @if ($canSelectSeller)
                    <div class="col-md-3">
                        <label class="form-label" for="user_id">Vendedor</label>
                        <select class="form-select" id="user_id" name="user_id">
                            @foreach ($sellers as $option)
                                <option value="{{ $option->id }}" @selected($option->id === $seller->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-auto">
                    <button class="btn btn-dark w-100" type="submit"><i class="bi bi-funnel-fill me-2"></i>Ver comisiones</button>
                </div>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="panel-card h-100">
                    <small class="text-secondary">Comisión a pagar</small>
                    <h2 class="h3 fw-bold text-success mb-0">${{ number_format($summary['total_commission'], 2) }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel-card h-100">
                    <small class="text-secondary">Notas vendidas</small>
                    <h2 class="h3 fw-bold mb-0">{{ $summary['orders_count'] }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel-card h-100">
                    <small class="text-secondary">Porcentaje asignado</small>
                    <h2 class="h3 fw-bold mb-0">{{ number_format($commissionPercentage, 2) }}%</h2>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">Notas incluidas</h2>
                    <p class="text-secondary mb-0">La comisión se calcula sobre cada nota sin incluir el costo de envío.</p>
                </div>
                <span class="badge text-bg-light">{{ $rows->count() }} notas</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nota</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Envío excluido</th>
                            <th class="text-end">Comisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td><a class="fw-bold text-dark" href="{{ route('admin.orders.show', $row['order']) }}">{{ $row['order']->folio }}</a></td>
                                <td>{{ $row['order']->customer->name }}</td>
                                <td class="text-secondary">{{ $row['order']->ordered_at->format('d/m/Y') }}</td>
                                <td>${{ number_format($row['shipping'], 2) }}</td>
                                <td class="text-end fw-bold text-success">${{ number_format($row['commission'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-secondary py-4" colspan="5">No hay notas vendidas en este rango.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($rows->isNotEmpty())
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total de comisión</th>
                                <th class="text-end text-success">${{ number_format($summary['total_commission'], 2) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
