@extends('layouts.app')

@section('title', 'Gastos | ForjaLab')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">Gastos reales</div>
                <h1 class="fw-bold mt-2 mb-0">Gastos</h1>
                <p class="text-secondary mb-0 mt-2">{{ $start->format('d/m/Y') }} al {{ $end->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            @can('orders.manage')
                <div class="col-lg-5">
                    <div class="panel-card h-100">
                        <div class="form-section-title compact">
                            <i class="bi bi-cash-stack"></i>
                            <div>
                                <h2>Registrar gasto</h2>
                                <p>Publicidad, insumos, traslados u otros gastos reales.</p>
                            </div>
                        </div>
                        <form class="row g-3" method="POST" action="{{ route('admin.expenses.store') }}">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label" for="spent_at">Fecha</label>
                                <input class="form-control @error('spent_at') is-invalid @enderror" id="spent_at" name="spent_at" type="date" value="{{ old('spent_at', now()->format('Y-m-d')) }}" required>
                                @error('spent_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="amount">Monto</label>
                                <input class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="category">Categoría</label>
                                <input class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', 'Publicidad') }}" maxlength="80">
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="concept">Concepto</label>
                                <input class="form-control @error('concept') is-invalid @enderror" id="concept" name="concept" value="{{ old('concept') }}" maxlength="160" placeholder="Campaña Facebook" required>
                                @error('concept')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="notes">Notas</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="1000">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button class="btn btn-dark w-100" type="submit"><i class="bi bi-save me-2"></i>Guardar gasto</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            <div class="@can('orders.manage') col-lg-7 @else col-12 @endcan">
                <div class="panel-card h-100">
                    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-end mb-3">
                        <div>
                            <small class="text-secondary">Gastos del mes</small>
                            <h2 class="h3 fw-bold text-danger mb-0">${{ number_format((float) $total, 2) }}</h2>
                        </div>
                        <form class="d-flex gap-2 align-items-end" method="GET">
                            <div>
                                <label class="form-label" for="month">Mes</label>
                                <input class="form-control" id="month" name="month" type="month" value="{{ $month->format('Y-m') }}">
                            </div>
                            <button class="btn btn-outline-dark" type="submit"><i class="bi bi-funnel-fill me-1"></i>Ver</button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th>Categoría</th>
                                    <th class="text-end">Monto</th>
                                    @can('orders.manage')<th class="text-end">Acciones</th>@endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td class="text-secondary">{{ $expense->spent_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $expense->concept }}</div>
                                            @if ($expense->notes)
                                                <small class="text-secondary">{{ $expense->notes }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge text-bg-light">{{ $expense->category }}</span></td>
                                        <td class="text-end fw-bold">${{ number_format((float) $expense->amount, 2) }}</td>
                                        @can('orders.manage')
                                            <td class="text-end">
                                                <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" data-confirm="Este gasto se quitará de los reportes." data-confirm-title="¿Eliminar gasto?" data-confirm-button="Sí, eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-secondary py-4" colspan="@can('orders.manage') 5 @else 4 @endcan">No hay gastos registrados en este mes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $expenses->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
