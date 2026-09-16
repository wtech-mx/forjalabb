<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Lista de compras | ForjaLab</title>
    <style>
        @page { margin: 34px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #25170f; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { padding-bottom: 18px; border-bottom: 3px solid #ed741d; }
        .brand { color: #5f873f; font-size: 13px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        h1 { margin: 7px 0 3px; font-size: 27px; }
        .subtitle { margin: 0; color: #74685f; }
        .summary { margin: 18px 0; padding: 13px 16px; background: #fff4e7; border: 1px solid #f0d4b4; }
        .summary strong { display: inline-block; margin-right: 8px; color: #bd401f; font-size: 22px; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 10px 9px; color: #fff; background: #25170f; font-size: 10px; text-align: left; text-transform: uppercase; }
        td { padding: 11px 9px; border-bottom: 1px solid #ddd3c8; vertical-align: top; }
        tbody tr:nth-child(even) { background: #faf7f2; }
        .product { font-size: 13px; font-weight: bold; }
        .variant { display: block; margin-top: 3px; color: #74685f; }
        .orders { color: #74685f; font-size: 10px; }
        .quantity { width: 90px; color: #b42318; font-size: 20px; font-weight: bold; text-align: center; }
        .empty { margin-top: 24px; padding: 28px; color: #287442; background: #eef9f1; border: 1px solid #bcdcc4; text-align: center; }
        .footer { margin-top: 18px; color: #82756c; font-size: 9px; text-align: right; }
    </style>
</head>
<body>
    <header class="header">
        <div class="brand">ForjaLab · Inventario</div>
        <h1>Lista de compras</h1>
        <p class="subtitle">Productos necesarios para completar los pedidos pendientes.</p>
    </header>

    @if($rows->isNotEmpty())
        <div class="summary"><strong>{{ number_format($totalUnits) }}</strong> pieza{{ $totalUnits === 1 ? '' : 's' }} por comprar en total.</div>
        <table>
            <thead><tr><th>Producto y variante</th><th>Pedidos relacionados</th><th style="text-align:center">Comprar</th></tr></thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td><span class="product">{{ $row['product'] }}</span><span class="variant">{{ $row['variant'] }}</span></td>
                        <td class="orders">{{ $row['orders']->join(', ') }}</td>
                        <td class="quantity">{{ $row['shortage'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty"><strong>No necesitas comprar inventario.</strong><br>Las existencias actuales cubren todos los pedidos pendientes.</div>
    @endif

    <div class="footer">Generado el {{ $generatedAt->format('d/m/Y H:i') }}</div>
</body>
</html>
