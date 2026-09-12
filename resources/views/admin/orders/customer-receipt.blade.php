<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo {{ $order->folio }}</title>
    <style>
        @page { margin: 32px 34px 44px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #29251f; font-size: 10.5px; line-height: 1.4; }
        .header { border-bottom: 4px solid #e87318; padding-bottom: 13px; margin-bottom: 16px; }
        .brand-table, .info-table, .items, .references, .totals { width: 100%; border-collapse: collapse; }
        .logo-wrap { width: 61px; height: 61px; padding: 4px; background: #fff; border: 2px solid #eadbca; border-radius: 50%; text-align: center; }
        .logo { width: 51px; height: 51px; object-fit: contain; vertical-align: middle; border-radius: 50%; }
        .brand-name { color: #e87318; font-size: 23px; font-weight: bold; }
        .folio { text-align: right; vertical-align: middle; }
        .folio strong { font-size: 15px; }
        .muted { color: #716a61; }
        .info-table { margin-bottom: 17px; }
        .info-table td { width: 50%; vertical-align: top; }
        .info-table td + td { padding-left: 12px; }
        .box { background: #f7f3ec; border: 1px solid #e8ded1; border-radius: 7px; padding: 11px 12px; min-height: 96px; }
        .label { color: #8b5a2b; font-size: 8.5px; font-weight: bold; letter-spacing: .6px; text-transform: uppercase; }
        .icon { display: inline-block; width: 17px; height: 17px; margin-right: 5px; border-radius: 50%; background: #e87318; color: #fff; font-size: 9px; font-weight: bold; line-height: 17px; text-align: center; vertical-align: middle; }
        .icon.green { background: #263f2c; }
        .icon.purple { background: #6f42c1; }
        .icon.gold { background: #b77416; }
        .delivery-badge { display: inline-block; margin: 6px 0; padding: 4px 9px; border-radius: 12px; background: #eee6fa; color: #5d3190; font-weight: bold; }
        .section-title { margin: 0 0 7px; font-size: 11px; letter-spacing: .4px; }
        .items { margin-bottom: 13px; }
        .items th { padding: 7px 6px; background: #263f2c; color: #fff; text-align: left; font-size: 9px; }
        .items td { padding: 7px 6px; border-bottom: 1px solid #e5ddd2; vertical-align: middle; }
        .items tr { page-break-inside: avoid; }
        .product-image { width: 39px; height: 39px; object-fit: cover; border-radius: 5px; }
        .placeholder { width: 39px; height: 39px; line-height: 39px; text-align: center; background: #f0e7da; color: #97795b; border-radius: 5px; font-weight: bold; }
        .right { text-align: right !important; }
        .center { text-align: center !important; }
        .detail { margin-top: 2px; color: #716a61; font-size: 8.8px; }
        .summary { width: 100%; margin-top: 5px; page-break-inside: avoid; }
        .summary td { vertical-align: top; }
        .summary-left { padding-right: 18px; }
        .totals { border: 1px solid #e2d7ca; }
        .totals td { padding: 5px 8px; border-bottom: 1px solid #eee7df; }
        .totals .grand td { padding-top: 7px; padding-bottom: 7px; background: #263f2c; color: #fff; font-size: 13px; font-weight: bold; }
        .totals .paid td { color: #24713d; font-weight: bold; }
        .totals .balance td { color: #b74720; font-size: 12px; font-weight: bold; }
        .notes { margin-bottom: 9px; padding: 9px 10px; border-left: 4px solid #e87318; background: #fff8ef; }
        .references { margin-top: 5px; }
        .references td { padding: 4px; vertical-align: top; }
        .reference-image { width: 65px; height: 65px; object-fit: cover; border: 1px solid #dfd5c9; border-radius: 5px; }
        .footer { position: fixed; left: 0; right: 0; bottom: -25px; border-top: 1px solid #ddd2c4; padding-top: 7px; text-align: center; color: #716a61; font-size: 8px; }
        .notice { margin-top: 12px; color: #716a61; font-size: 8.5px; }
    </style>
</head>
<body>
<div class="header">
    <table class="brand-table"><tr>
        <td style="width:72px">@if($logoSource)<div class="logo-wrap"><img class="logo" src="{{ $logoSource }}" alt="Logotipo de ForjaLab"></div>@endif</td>
        <td><div class="brand-name">ForjaLab</div><div>Productos personalizados · Ciudad de México</div><div class="muted">WhatsApp 55 6444 2949 · forjalab.com.mx</div></td>
        <td class="folio"><strong>RECIBO DE PEDIDO</strong><br><span class="muted">Folio {{ $order->folio }}<br>{{ $order->ordered_at?->format('d/m/Y') }}</span></td>
    </tr></table>
</div>

<table class="info-table"><tr>
    <td><div class="box"><div class="label"><span class="icon">C</span>Datos del cliente</div><strong>{{ $order->customer?->name ?: 'Cliente' }}</strong>@if($order->customer?->company)<br>{{ $order->customer->company }}@endif
        @if($order->customer?->phone)<br>☎ {{ $order->customer->phone }}@endif
        @if($order->customer?->whatsapp && $order->customer->whatsapp !== $order->customer->phone)<br>WhatsApp: {{ $order->customer->whatsapp }}@endif
        @if($order->customer?->email)<br>✉ {{ $order->customer->email }}@endif
        @if($order->customer?->address)<br>⌂ {{ $order->customer->address }}@endif
    </div></td>
    <td><div class="box"><div class="label"><span class="icon purple">→</span>Entrega</div>
        <span class="delivery-badge">{{ $deliveryMethods[$order->delivery_method] ?? 'Por definir' }}</span><br>
        Fecha: {{ $order->delivery_at?->format('d/m/Y') ?: 'Por definir' }}@if($order->delivery_time) · {{ $order->delivery_time->format('H:i') }} h @endif
        @if($order->delivery_place)<br>Lugar: {{ $order->delivery_place }}@endif
        @if($order->delivery_method === 'skydropx' && $order->shipment)<br>Paquetería: {{ $order->shipment->carrier ?: ($order->shipment->quoted_service ?: 'Por definir') }}@if($order->shipment->tracking_number)<br>Guía: {{ $order->shipment->tracking_number }}@endif @endif
    </div></td>
</tr></table>

<h2 class="section-title"><span class="icon green">P</span>PRODUCTOS</h2>
<table class="items">
    <thead><tr><th style="width:46px"></th><th>Producto / detalle</th><th class="center" style="width:48px">Cant.</th><th class="right" style="width:82px">Precio</th><th class="right" style="width:82px">Importe</th></tr></thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>@if($item->pdf_image_source)<img class="product-image" src="{{ $item->pdf_image_source }}" alt="">@else<div class="placeholder">{{ $item->item_type === 'bundle' ? 'P' : 'F' }}</div>@endif</td>
            <td><strong>{{ $item->product_name }}</strong>
                @if($item->item_type === 'bundle' && $item->contents_snapshot)<div class="detail">Incluye: {!! nl2br(e($item->contents_snapshot)) !!}</div>
                @elseif($item->sale_package_name)<div class="detail">{{ $item->sale_package_name }}@if($item->sale_package_quantity) · {{ $item->sale_package_quantity }} pieza{{ $item->sale_package_quantity == 1 ? '' : 's' }} por paquete @endif</div>@endif
                @if(!empty($item->selected_colors))<div class="detail">Colores / piezas: {{ collect($item->selected_colors)->map(fn ($color, $index) => 'Pieza '.($index + 1).': '.str($color)->headline())->join(' · ') }}</div>@endif
            </td>
            <td class="center">{{ $item->quantity }}</td><td class="right">${{ number_format((float) $item->unit_price, 2) }}</td><td class="right"><strong>${{ number_format((float) $item->line_total, 2) }}</strong></td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="summary"><tr>
    <td class="summary-left" style="width:54%">
        @if($order->observations)<div class="notes"><div class="label"><span class="icon gold">!</span>Observaciones</div>{!! nl2br(e($order->observations)) !!}</div>@endif
        @if($order->references->isNotEmpty())
            <div class="label"><span class="icon purple">R</span>Referencias del pedido</div>
            <table class="references"><tr>
                @foreach($order->references->take(4) as $reference)
                    <td>@if($reference->pdf_image_source)<img class="reference-image" src="{{ $reference->pdf_image_source }}" alt=""><br>@endif<span class="muted">{{ $reference->label ?: ($reference->type === 'link' ? 'Referencia en línea' : 'Referencia') }}</span>@if($reference->type === 'link' && $reference->url)<br><span style="font-size:7px">{{ str($reference->url)->limit(42) }}</span>@endif</td>
                @endforeach
            </tr></table>
        @endif
    </td>
    <td style="width:46%"><div class="label" style="margin-bottom:5px"><span class="icon green">$</span>Resumen de pago</div><table class="totals">
        <tr><td>Subtotal</td><td class="right">${{ number_format((float) $order->subtotal, 2) }}</td></tr>
        <tr><td>Descuento</td><td class="right">-${{ number_format((float) $order->discount_amount, 2) }}</td></tr>
        <tr><td>Costo de envío</td><td class="right">${{ number_format((float) $order->shipping_cost, 2) }}</td></tr>
        <tr class="grand"><td>Total</td><td class="right">${{ number_format((float) $order->total, 2) }}</td></tr>
        <tr class="paid"><td>Pagado total</td><td class="right">${{ number_format((float) $order->advance_payment, 2) }}</td></tr>
        <tr class="balance"><td>Restante / liquidar</td><td class="right">${{ number_format((float) $order->balance_due, 2) }}</td></tr>
    </table></td>
</tr></table>

<div class="notice">Este recibo resume tu pedido y sus pagos registrados. No es un comprobante fiscal. Conserva el folio para cualquier aclaración.</div>
<div class="footer">ForjaLab · Gracias por tu compra · WhatsApp 55 6444 2949 · forjalab.com.mx</div>
</body>
</html>
