@extends('layouts.app')

@php
    $isEdit = $bundle->exists;
    $itemRows = old('items', $bundle->items->map(fn ($item) => [
        'catalog_product_id' => $item->catalog_product_id,
        'quantity' => $item->quantity,
    ])->values()->all());
    $itemRows = count($itemRows) ? $itemRows : [['catalog_product_id' => '', 'quantity' => 1]];
    $productCosts = $products->mapWithKeys(fn ($product) => [$product->id => (float) $product->cost_subtotal])->all();
@endphp

@section('title', ($isEdit ? 'Editar paquete' : 'Nuevo paquete').' | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">{{ $isEdit ? 'Editar' : 'Nuevo' }}</div>
                    <h1 class="fw-bold mt-2 mb-0">Paquete de productos</h1>
                </div>
                <a class="btn btn-outline-dark" href="{{ route('admin.packages.index') }}"><i class="bi bi-arrow-left me-2"></i>Volver</a>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.packages.update', $bundle) : route('admin.packages.store') }}" enctype="multipart/form-data" data-bundle-form data-product-costs='@json($productCosts)'>
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-box-seam-fill"></i>
                                <div>
                                    <h2>Datos principales</h2>
                                    <p>El slug y la URL se generan automaticamente con el nombre.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="name">Nombre</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $bundle->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="description">Descripcion</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $bundle->description) }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="cover_photo">Foto principal</label>
                                    <input class="form-control @error('cover_photo') is-invalid @enderror" id="cover_photo" name="cover_photo" type="file" accept="image/*">
                                    @error('cover_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($bundle->image_url)
                                        <div class="form-text">Actual: {{ $bundle->cover_photo_path }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="gallery_photos">Galeria</label>
                                    <input class="form-control @error('gallery_photos.*') is-invalid @enderror" id="gallery_photos" name="gallery_photos[]" type="file" accept="image/*" multiple>
                                    @error('gallery_photos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($bundle->photos->isNotEmpty())
                                        <div class="form-text">{{ $bundle->photos->count() }} foto(s) cargadas.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-bag-plus-fill"></i>
                                <div>
                                    <h2>Productos del paquete</h2>
                                    <p>Selecciona productos y cantidad. Se toma el costo base del producto, sin su empaque individual.</p>
                                </div>
                            </div>
                            <div class="bundle-item-table" data-bundle-item-list>
                                @foreach ($itemRows as $index => $item)
                                    <div class="bundle-item-row" data-bundle-item-row>
                                        <select class="form-select" name="items[{{ $index }}][catalog_product_id]" data-bundle-product>
                                            <option value="">Producto</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" @selected((string) ($item['catalog_product_id'] ?? '') === (string) $product->id)>
                                                    {{ $product->name }} · costo ${{ number_format((float) $product->cost_subtotal, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input class="form-control" name="items[{{ $index }}][quantity]" type="number" min="1" value="{{ $item['quantity'] ?? 1 }}" placeholder="Cantidad" data-bundle-quantity>
                                        <div class="package-result">
                                            <strong data-bundle-row-cost>$0.00</strong>
                                            <small>Costo de linea</small>
                                        </div>
                                        <button class="btn btn-outline-danger" type="button" data-remove-bundle-row><i class="bi bi-x-lg"></i></button>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-outline-dark btn-sm mt-3" type="button" data-add-bundle-item>
                                <i class="bi bi-plus-circle me-2"></i>Agregar producto
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="panel-card sticky-copy">
                            <div class="form-section-title compact">
                                <i class="bi bi-calculator-fill"></i>
                                <div>
                                    <h2>Desglose</h2>
                                    <p>El empaque es propio de este paquete.</p>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label class="form-label" for="packaging_cost">Empaque del paquete</label>
                                    <input class="form-control" id="packaging_cost" name="packaging_cost" type="number" min="0" step="0.01" value="{{ old('packaging_cost', $bundle->packaging_cost ?? 0) }}" data-bundle-packaging>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="family_multiplier">Margen familiar</label>
                                    <input class="form-control" id="family_multiplier" name="family_multiplier" type="number" min="1" step="0.01" value="{{ old('family_multiplier', $bundle->family_multiplier ?? 1.5) }}" data-family-multiplier>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="public_multiplier">Margen publico</label>
                                    <input class="form-control" id="public_multiplier" name="public_multiplier" type="number" min="1" step="0.01" value="{{ old('public_multiplier', $bundle->public_multiplier ?? 1.8) }}" data-public-multiplier>
                                </div>
                            </div>
                            <div class="price-summary">
                                <div><span>Productos</span><strong data-items-cost>$0.00</strong></div>
                                <div><span>Empaque</span><strong data-packaging-cost>$0.00</strong></div>
                                <div><span>Costo total</span><strong data-total-cost>$0.00</strong></div>
                                <div><span>Familiar</span><strong data-family-price>$0.00</strong><small data-family-profit>Ganancia $0.00</small></div>
                                <div><span>Publico</span><strong data-public-price>$0.00</strong><small data-public-profit>Ganancia $0.00</small></div>
                            </div>
                            <div class="form-check form-switch mt-4 mb-3">
                                <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $bundle->is_active))>
                                <label class="form-check-label" for="is_active">Activo en pagina</label>
                            </div>
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" id="is_featured" name="is_featured" type="checkbox" value="1" @checked(old('is_featured', $bundle->is_featured))>
                                <label class="form-check-label" for="is_featured">Destacado ancho</label>
                            </div>
                            <button class="btn btn-dark w-100" type="submit">
                                <i class="bi bi-save me-2"></i>{{ $isEdit ? 'Guardar paquete' : 'Crear paquete' }}
                            </button>
                            @if ($isEdit)
                                <a class="btn btn-outline-dark w-100 mt-2" href="{{ route('admin.packages.preview', $bundle) }}">
                                    <i class="bi bi-eye me-2"></i>Vista previa
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <template data-bundle-item-template>
        <div class="bundle-item-row" data-bundle-item-row>
            <select class="form-select" name="items[__INDEX__][catalog_product_id]" data-bundle-product>
                <option value="">Producto</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} · costo ${{ number_format((float) $product->cost_subtotal, 2) }}</option>
                @endforeach
            </select>
            <input class="form-control" name="items[__INDEX__][quantity]" type="number" min="1" value="1" placeholder="Cantidad" data-bundle-quantity>
            <div class="package-result">
                <strong data-bundle-row-cost>$0.00</strong>
                <small>Costo de linea</small>
            </div>
            <button class="btn btn-outline-danger" type="button" data-remove-bundle-row><i class="bi bi-x-lg"></i></button>
        </div>
    </template>

    <script>
        (() => {
            const form = document.querySelector('[data-bundle-form]');
            if (!form) return;

            const money = (value) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 }).format(value);
            const roundPriceUp = (value) => Math.ceil(Number.parseFloat(value) || 0);
            const costs = JSON.parse(form.dataset.productCosts || '{}');
            const list = form.querySelector('[data-bundle-item-list]');
            const template = document.querySelector('[data-bundle-item-template]').innerHTML;

            const calculate = () => {
                let itemsCost = 0;
                form.querySelectorAll('[data-bundle-item-row]').forEach((row) => {
                    const productId = row.querySelector('[data-bundle-product]')?.value;
                    const quantity = Math.max(1, Number.parseInt(row.querySelector('[data-bundle-quantity]')?.value, 10) || 1);
                    const rowCost = (Number.parseFloat(costs[productId]) || 0) * quantity;
                    itemsCost += rowCost;
                    row.querySelector('[data-bundle-row-cost]').textContent = money(rowCost);
                });

                const packaging = Math.max(0, Number.parseFloat(form.querySelector('[data-bundle-packaging]')?.value) || 0);
                const total = itemsCost + packaging;
                const familyMultiplier = Math.max(1, Number.parseFloat(form.querySelector('[data-family-multiplier]')?.value) || 1);
                const publicMultiplier = Math.max(1, Number.parseFloat(form.querySelector('[data-public-multiplier]')?.value) || 1);
                const family = roundPriceUp(total * familyMultiplier);
                const publicPrice = roundPriceUp(total * publicMultiplier);

                form.querySelector('[data-items-cost]').textContent = money(itemsCost);
                form.querySelector('[data-packaging-cost]').textContent = money(packaging);
                form.querySelector('[data-total-cost]').textContent = money(total);
                form.querySelector('[data-family-price]').textContent = money(family);
                form.querySelector('[data-family-profit]').textContent = `Ganancia ${money(family - total)}`;
                form.querySelector('[data-public-price]').textContent = money(publicPrice);
                form.querySelector('[data-public-profit]').textContent = `Ganancia ${money(publicPrice - total)}`;
            };

            form.addEventListener('input', (event) => {
                if (event.target.matches('[data-bundle-quantity], [data-bundle-packaging], [data-family-multiplier], [data-public-multiplier]')) {
                    calculate();
                }
            });

            form.addEventListener('change', (event) => {
                if (event.target.matches('[data-bundle-product]')) {
                    calculate();
                }
            });

            form.addEventListener('click', (event) => {
                if (event.target.closest('[data-remove-bundle-row]')) {
                    event.target.closest('[data-bundle-item-row]')?.remove();
                    calculate();
                }
            });

            form.querySelector('[data-add-bundle-item]')?.addEventListener('click', () => {
                list.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', Date.now()));
                calculate();
            });

            calculate();
        })();
    </script>
@endsection
