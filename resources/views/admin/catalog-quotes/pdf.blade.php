<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px 40px 55px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #242424; }
        .brand { font-size: 25px; font-weight: bold; color: #171717; }
        .header { border-bottom: 3px solid #e8ae45; padding-bottom: 14px; margin-bottom: 20px; }
        h1 { font-size: 18px; margin: 8px 0; }
        .muted { color: #666; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 18px; }
        th { background: #202020; color: white; text-align: left; padding: 10px 8px; font-size: 9px; }
        td { padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; overflow-wrap: break-word; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .price { font-weight: bold; color: #71501b; }
        .photo { text-align: center; vertical-align: middle; }
        .thumbnail { max-width: 54px; max-height: 54px; }
        .notes { margin-top: 22px; white-space: pre-wrap; overflow-wrap: break-word; }
        .conditions { margin-top: 18px; color: #666; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">ForjaLab</div>
        <h1>Catálogo de precios y mayoreo</h1>
        <div class="muted">Emitido el {{ $generatedAt->format('d/m/Y') }} | Precios en pesos mexicanos (MXN)</div>
    </div>
    @if($recipient)<p><strong>Preparado para:</strong> {{ $recipient }}</p>@endif
    <table>
        <thead><tr><th style="width: 12%">Portada</th><th style="width: 30%">Producto / presentación</th><th style="width: 18%">Precio en página</th><th style="width: 18%">Precio ofrecido</th><th style="width: 22%">Mayoreo desde</th></tr></thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td class="photo">@if($row['image'] ?? null)<img class="thumbnail" src="{{ $row['image'] }}" alt="Portada de {{ $row['product']->name }}">@else<span class="muted">Sin foto</span>@endif</td>
                    <td><strong>{{ $row['product']->name }}</strong><br><span class="muted">{{ $row['presentation'] }}</span></td>
                    <td>{{ $row['public_price'] > 0 ? '$'.number_format((float) $row['public_price'], 2) : 'Por cotizar' }}</td>
                    <td class="price">${{ number_format((float) $row['price'], 2) }}</td>
                    <td>{{ number_format((int) $row['minimum_quantity']) }} {{ (int) $row['minimum_quantity'] === 1 ? 'pieza' : 'piezas' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="conditions">El precio ofrecido es por la presentación indicada y aplica a partir del mínimo de piezas de mayoreo de cada producto. El precio en página corresponde al catálogo al momento de generar este documento.</p>
    @if($notes)<div class="notes"><strong>Notas y condiciones</strong><br>{{ $notes }}</div>@endif
</body>
</html>
