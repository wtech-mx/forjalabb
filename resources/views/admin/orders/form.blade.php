@extends('layouts.app')
@section('title', ($order->exists ? 'Editar' : 'Nuevo').' pedido | ForjaLab')
@section('content')
@php
    $savedItems = old('items', $order->exists ? $order->items->map(fn ($item) => [
        'item_type' => $item->item_type,
        'item_id' => $item->item_type === 'bundle' ? $item->catalog_bundle_id : $item->catalog_product_id,
        'sale_package_id' => $item->catalog_product_sale_package_id,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
    ])->all() : []);

    $productPackageMap = $products->mapWithKeys(fn ($product) => [
        $product->id => $product->salePackages->map(fn ($package) => [
            'id' => $package->id,
            'name' => $package->name,
            'quantity' => $package->quantity,
            'unit_price' => (float) $package->unit_public_price,
            'total_price' => (float) $package->public_price,
            'is_default' => (bool) $package->is_default,
        ])->values(),
    ]);

    $deliveryTime = old('delivery_time', $order->delivery_time ? \Illuminate\Support\Carbon::parse($order->delivery_time)->format('H:i') : '');
    $referenceLinks = old('reference_links', ['', '']);
@endphp
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">{{ $order->exists ? $order->folio : 'Captura de venta' }}</div>
                <h1 class="fw-bold mt-2 mb-0">{{ $order->exists ? 'Editar pedido' : 'Nuevo pedido' }}</h1>
            </div>
        </div>

        <form class="order-editor" method="POST" enctype="multipart/form-data" action="{{ $order->exists ? route('admin.orders.update', $order) : route('admin.orders.store') }}" data-order-form data-create-customer="{{ old('new_customer_name') ? '1' : '0' }}">
            @csrf
            @if($order->exists) @method('PUT') @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="panel-card order-editor-card order-editor-customer mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 fw-bold mb-0 order-editor-title"><span><i class="bi bi-person-vcard-fill"></i></span>¿A quién se le entregará?</h2>
                            <button class="btn btn-sm btn-outline-dark" type="button" data-new-customer-toggle><i class="bi bi-person-plus me-1"></i>Crear cliente</button>
                        </div>
                        <div data-existing-customer>
                            <label class="form-label">Buscar o seleccionar cliente</label>
                            <input class="form-control mb-2" type="search" placeholder="Escribe nombre, teléfono o correo" data-customer-search>
                            <select class="form-select" name="customer_id" data-customer-select>
                                <option value="">Selecciona un cliente</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" data-search="{{ Str::lower($customer->name.' '.$customer->phone.' '.$customer->email) }}" @selected(old('customer_id', $order->customer_id) == $customer->id)>{{ $customer->name }}{{ $customer->phone ? ' · '.$customer->phone : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 d-none" data-new-customer>
                            <div class="col-md-6"><label class="form-label">Nombre *</label><input class="form-control" name="new_customer_name" value="{{ old('new_customer_name') }}" disabled></div>
                            <div class="col-md-6"><label class="form-label">Teléfono</label><input class="form-control" type="tel" name="new_customer_phone" value="{{ old('new_customer_phone') }}" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" placeholder="5564442949" data-phone-10 disabled><small class="text-secondary">10 dígitos, sin espacios.</small></div>
                            <div class="col-md-6"><label class="form-label">Correo</label><input class="form-control" type="email" name="new_customer_email" value="{{ old('new_customer_email') }}" disabled></div>
                            <div class="col-md-6"><label class="form-label">Dirección</label><input class="form-control" name="new_customer_address" value="{{ old('new_customer_address') }}" disabled></div>
                        </div>
                    </div>

                    <div class="panel-card order-editor-card order-editor-products mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 fw-bold mb-0 order-editor-title"><span><i class="bi bi-bag-check-fill"></i></span>Productos y paquetes</h2>
                            <button class="btn btn-sm btn-dark" type="button" data-add-item><i class="bi bi-plus-lg me-1"></i>Agregar concepto</button>
                        </div>
                        <div class="order-items" data-order-items></div>
                    </div>

                    <div class="panel-card order-editor-card order-editor-references mb-4">
                        <h2 class="h5 fw-bold order-editor-title"><span><i class="bi bi-images"></i></span>Referencias del cliente</h2>
                        <p class="text-secondary mb-3">Agrega fotos que mande el cliente o links de inspiración para este pedido.</p>
                        @if($order->exists && $order->references->isNotEmpty())
                            <div class="order-reference-grid mb-3">
                                @foreach($order->references as $reference)
                                    <label class="order-reference-card">
                                        @if($reference->type === 'image')
                                            <img src="{{ $reference->display_url }}" alt="{{ $reference->label }}">
                                        @else
                                            <span><i class="bi bi-link-45deg"></i>{{ $reference->label ?: $reference->url }}</span>
                                        @endif
                                        <small><input class="form-check-input me-1" type="checkbox" name="remove_references[]" value="{{ $reference->id }}">Quitar</small>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        <label class="form-label">Subir fotos de referencia</label>
                        <input class="form-control mb-3" type="file" name="reference_files[]" accept="image/jpeg,image/png,image/webp" multiple>
                        <label class="form-label">Links de referencia</label>
                        <div class="row g-2">
                            @foreach($referenceLinks as $link)
                                <div class="col-md-6"><input class="form-control" type="url" name="reference_links[]" value="{{ $link }}" placeholder="https://..."></div>
                            @endforeach
                            <div class="col-md-6"><input class="form-control" type="url" name="reference_links[]" placeholder="https://..."></div>
                            <div class="col-md-6"><input class="form-control" type="url" name="reference_links[]" placeholder="https://..."></div>
                        </div>
                    </div>

                    <div class="panel-card order-editor-card order-editor-notes">
                        <h2 class="h5 fw-bold order-editor-title"><span><i class="bi bi-chat-left-text-fill"></i></span>Observaciones</h2>
                        <textarea class="form-control" name="observations" rows="4" placeholder="Colores, personalización, acuerdos o indicaciones especiales...">{{ old('observations', $order->observations) }}</textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="panel-card order-editor-card order-editor-delivery mb-4">
                        <h2 class="h5 fw-bold order-editor-title"><span><i class="bi bi-truck-front-fill"></i></span>Entrega y estado</h2>
                        <label class="form-label">Fecha del pedido</label>
                        <input class="form-control mb-3" type="date" name="ordered_at" value="{{ old('ordered_at', $order->ordered_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                        <label class="form-label">Día de entrega</label>
                        <input class="form-control mb-3" type="date" name="delivery_at" value="{{ old('delivery_at', $order->delivery_at?->format('Y-m-d')) }}">
                        <label class="form-label">Hora de entrega</label>
                        <input class="form-control mb-3" type="time" name="delivery_time" value="{{ $deliveryTime }}">
                        <label class="form-label">Lugar de entrega</label>
                        <textarea class="form-control mb-3" name="delivery_place" rows="3" placeholder="Dirección, punto de encuentro o indicaciones">{{ old('delivery_place', $order->delivery_place) }}</textarea>
                        <label class="form-label">Ubicacion de Google Maps</label>
                        <textarea class="form-control mb-2" name="delivery_map_url" rows="3" placeholder="Pega aqui el link de Maps o el iframe completo">{{ old('delivery_map_url', $order->delivery_map_url) }}</textarea>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <input class="form-control" type="number" step="0.0000001" name="delivery_lat" value="{{ old('delivery_lat', $order->delivery_lat) }}" placeholder="Latitud">
                            </div>
                            <div class="col-6">
                                <input class="form-control" type="number" step="0.0000001" name="delivery_lng" value="{{ old('delivery_lng', $order->delivery_lng) }}" placeholder="Longitud">
                            </div>
                            <div class="col-12">
                                <small class="text-secondary">Si el link de Maps trae coordenadas, se llenan al guardar. Si es link corto, pega latitud y longitud.</small>
                            </div>
                        </div>
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="status">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="panel-card order-editor-card order-editor-summary order-summary">
                        <h2 class="h5 fw-bold order-editor-title"><span><i class="bi bi-calculator-fill"></i></span>Resumen</h2>
                        <div class="row g-2 mb-3">
                            <div class="col-5"><label class="form-label">Descuento</label><select class="form-select" name="discount_type" data-discount-type><option value="fixed" @selected(old('discount_type', $order->discount_type) === 'fixed')>$ MXN</option><option value="percent" @selected(old('discount_type', $order->discount_type) === 'percent')>%</option></select></div>
                            <div class="col-7"><label class="form-label">Cantidad</label><input class="form-control" type="number" min="0" step="0.01" name="discount_value" value="{{ old('discount_value', $order->discount_value ?? 0) }}" data-discount></div>
                        </div>
                        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" value="1" name="has_shipping" id="hasShipping" data-shipping-toggle @checked(old('has_shipping', $order->has_shipping))><label class="form-check-label" for="hasShipping">Este pedido lleva envío</label></div>
                        <div class="mb-3 {{ old('has_shipping', $order->has_shipping) ? '' : 'd-none' }}" data-shipping-wrap><label class="form-label">Costo de envío</label><input class="form-control" type="number" min="0" step="0.01" name="shipping_cost" value="{{ old('shipping_cost', $order->shipping_cost ?? 0) }}" data-shipping></div>
                        <label class="form-label">Anticipo pagado</label>
                        <input class="form-control mb-3" type="number" min="0" step="0.01" name="advance_payment" value="{{ old('advance_payment', $order->advance_payment ?? 0) }}" data-advance>
                        <label class="form-label">Restante / liquidar ahora</label>
                        <input class="form-control mb-3" type="number" min="0" step="0.01" name="payment_received" value="{{ old('payment_received', 0) }}" data-payment-received>
                        <div class="order-totals"><div><span>Subtotal</span><strong data-subtotal>$0.00</strong></div><div><span>Descuento</span><strong data-discount-total>-$0.00</strong></div><div><span>Envío</span><strong data-shipping-total>$0.00</strong></div><div class="total"><span>Total</span><strong data-total>$0.00</strong></div><div><span>Pagado total</span><strong data-advance-total>$0.00</strong></div><div class="balance"><span>Saldo</span><strong data-balance>$0.00</strong></div></div>
                        <button class="btn btn-dark btn-lg w-100 mt-4"><i class="bi bi-check2-circle me-2"></i>{{ $order->exists ? 'Guardar cambios' : 'Crear pedido' }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<template id="orderItemTemplate">
    <div class="order-item-row" data-order-item>
        <div>
            <label class="form-label">Producto o paquete</label>
            <select class="form-select" data-product required>
                <option value="">Selecciona...</option>
                <optgroup label="Productos">
                    @foreach($products as $product)
                        <option value="product:{{ $product->id }}" data-type="product" data-id="{{ $product->id }}" data-price="{{ $product->public_price }}">{{ $product->name }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Paquetes">
                    @foreach($bundles as $bundle)
                        <option value="bundle:{{ $bundle->id }}" data-type="bundle" data-id="{{ $bundle->id }}" data-price="{{ $bundle->public_price }}" data-contents="{{ $bundle->items->map(fn ($item) => $item->quantity.' × '.($item->product?->name ?? 'Producto'))->join(' · ') }}">Paquete: {{ $bundle->name }}{{ $bundle->is_active ? '' : ' (no publicado)' }}</option>
                    @endforeach
                </optgroup>
            </select>
            <input type="hidden" name="items[__INDEX__][item_type]" data-item-type>
            <input type="hidden" name="items[__INDEX__][item_id]" data-item-id>
            <small class="order-package-contents" data-item-contents></small>
        </div>
        <div data-sale-package-wrap>
            <label class="form-label">Precio del producto</label>
            <select class="form-select" name="items[__INDEX__][sale_package_id]" data-sale-package disabled>
                <option value="">Precio base</option>
            </select>
        </div>
        <div>
            <label class="form-label">Cantidad</label>
            <input class="form-control" type="number" min="1" value="1" name="items[__INDEX__][quantity]" data-quantity required>
        </div>
        <div>
            <label class="form-label">Precio unitario</label>
            <input class="form-control" type="number" min="0" step="0.01" name="items[__INDEX__][unit_price]" data-unit-price required>
        </div>
        <div>
            <label class="form-label">Importe</label>
            <strong class="order-line-total" data-line-total>$0.00</strong>
        </div>
        <button class="btn btn-sm btn-outline-danger" type="button" data-remove-item aria-label="Eliminar concepto"><i class="bi bi-trash"></i></button>
    </div>
</template>
<script type="application/json" id="orderProductPackages">{!! json_encode($productPackageMap) !!}</script>
<script type="application/json" id="savedOrderItems">{!! json_encode($savedItems) !!}</script>
@endsection
