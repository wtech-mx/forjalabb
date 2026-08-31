<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comisiones {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f130d; font-size: 12px; }
        h1 { margin: 0 0 4px; font-size: 28px; }
        .muted { color: #6f6259; }
        .header { border-bottom: 2px solid #1f130d; padding-bottom: 14px; margin-bottom: 18px; }
        .summary { width: 100%; margin-bottom: 18px; border-collapse: collapse; }
        .summary td { width: 33.33%; border: 1px solid #e8ded2; padding: 12px; vertical-align: top; }
        .label { color: #6f6259; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
        .value { font-size: 20px; font-weight: 700; margin-top: 4px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #1f130d; color: #fff; text-align: left; padding: 8px; }
        table.data td { border-bottom: 1px solid #ece3da; padding: 8px; }
        .right { text-align: right; }
        .total-row td { font-weight: 700; border-top: 2px solid #1f130d; }
    </style>
</head>
<body>
    <div class="header">
        <div class="muted">ForjaLab · Reporte</div>
        <h1>Comisiones</h1>
        <div class="muted">{{ $start->format('d/m/Y') }} al {{ $end->format('d/m/Y') }} · {{ $seller->name }}</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Comisión a pagar</div>
                <div class="value">${{ number_format($summary['total_commission'], 2) }}</div>
            </td>
            <td>
                <div class="label">Notas vendidas</div>
                <div class="value">{{ $summary['orders_count'] }}</div>
            </td>
            <td>
                <div class="label">Porcentaje asignado</div>
                <div class="value">{{ number_format($commissionPercentage, 2) }}%</div>
            </td>
        </tr>
    </table>

    <p class="muted">La comisión se calcula sobre cada nota sin incluir el costo de envío.</p>

    <table class="data">
        <thead>
            <tr>
                <th>Nota</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th class="right">Envío excluido</th>
                <th class="right">Comisión</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['order']->folio }}</td>
                    <td>{{ $row['order']->customer->name }}</td>
                    <td>{{ $row['order']->ordered_at->format('d/m/Y') }}</td>
                    <td class="right">${{ number_format($row['shipping'], 2) }}</td>
                    <td class="right">${{ number_format($row['commission'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay notas vendidas en este rango.</td>
                </tr>
            @endforelse
            @if ($rows->isNotEmpty())
                <tr class="total-row">
                    <td colspan="4" class="right">Total de comisión</td>
                    <td class="right">${{ number_format($summary['total_commission'], 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
