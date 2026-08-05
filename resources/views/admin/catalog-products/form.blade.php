@extends('layouts.app')

@php
    $isEdit = $product->exists;
    $costRows = old('costs', $product->costs->map(fn ($cost) => [
        'name' => $cost->name,
        'cost' => $cost->cost,
    ])->values()->all());
    $costRows = count($costRows) ? $costRows : [['name' => '', 'cost' => '']];
    $packageRows = old('sale_packages', $product->salePackages->map(fn ($package) => [
        'name' => $package->name,
        'quantity' => $package->quantity,
        'packaging_cost' => $package->packaging_cost,
        'family_multiplier' => $package->family_multiplier,
        'public_multiplier' => $package->public_multiplier,
        'is_default' => $package->is_default,
    ])->values()->all());
    $packageRows = count($packageRows) ? $packageRows : [
        ['name' => 'Individual', 'quantity' => 1, 'packaging_cost' => 0, 'family_multiplier' => 1.50, 'public_multiplier' => 1.80, 'is_default' => true],
        ['name' => 'Juego de 3', 'quantity' => 3, 'packaging_cost' => 0, 'family_multiplier' => 1.50, 'public_multiplier' => 1.70, 'is_default' => false],
        ['name' => 'Juego de 6', 'quantity' => 6, 'packaging_cost' => 0, 'family_multiplier' => 1.50, 'public_multiplier' => 1.60, 'is_default' => false],
    ];
    $optionRows = old('options', $product->options->map(fn ($option) => [
        'group' => $option->group,
        'name' => $option->name,
        'stock' => $option->stock,
        'existing_image_path' => $option->image_path,
    ])->values()->all());
    $optionRows = count($optionRows) ? $optionRows : [['group' => 'tipo', 'name' => '', 'stock' => 0, 'existing_image_path' => '']];
@endphp

@section('title', ($isEdit ? 'Editar producto' : 'Nuevo producto').' | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">{{ $isEdit ? 'Editar' : 'Nuevo' }}</div>
                    <h1 class="fw-bold mt-2 mb-0">Producto de catalogo</h1>
                </div>
                <a class="btn btn-outline-dark" href="{{ route('admin.catalog.index') }}"><i class="bi bi-arrow-left me-2"></i>Volver</a>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.catalog.update', $product) : route('admin.catalog.store') }}" enctype="multipart/form-data" data-product-form>
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-bag-heart-fill"></i>
                                <div>
                                    <h2>Datos principales</h2>
                                    <p>El slug, URL publica y orden se generan automaticamente.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="name">Nombre</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="description">Descripcion</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="specifications">Medidas, capacidad o especificaciones</label>
                                    <input class="form-control @error('specifications') is-invalid @enderror" id="specifications" name="specifications" value="{{ old('specifications', $product->specifications) }}" placeholder="Ej. 20 x 30 cm, 750 ml, acero inoxidable, set de 4 piezas">
                                    @error('specifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="cover_photo">Foto de portada</label>
                                    <input class="form-control @error('cover_photo') is-invalid @enderror" id="cover_photo" name="cover_photo" type="file" accept="image/*">
                                    @error('cover_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($product->image_url)
                                        <div class="form-text">Actual: {{ $product->cover_photo_path ?: $product->image_path }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="presentation_mode">Modo de presentacion</label>
                                    <select class="form-select @error('presentation_mode') is-invalid @enderror" id="presentation_mode" name="presentation_mode">
                                        @foreach ($presentationModes as $value => $label)
                                            <option value="{{ $value }}" @selected(old('presentation_mode', $product->presentation_mode) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('presentation_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-receipt-cutoff"></i>
                                <div>
                                    <h2>Costos e insumos</h2>
                                    <p>Agrega cada insumo. El sistema calcula subtotal, precios y ganancia.</p>
                                </div>
                            </div>
                            <div class="cost-table" data-cost-list>
                                @foreach ($costRows as $index => $cost)
                                    <div class="cost-row" data-cost-row>
                                        <input class="form-control" name="costs[{{ $index }}][name]" value="{{ $cost['name'] ?? '' }}" placeholder="Insumo">
                                        <input class="form-control" name="costs[{{ $index }}][cost]" type="number" min="0" step="0.01" value="{{ $cost['cost'] ?? '' }}" placeholder="Costo" data-cost-input>
                                        <button class="btn btn-outline-danger" type="button" data-remove-row><i class="bi bi-x-lg"></i></button>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-outline-dark btn-sm mt-3" type="button" data-add-cost>
                                <i class="bi bi-plus-circle me-2"></i>Agregar insumo
                            </button>
                        </div>

                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-boxes"></i>
                                <div>
                                    <h2>Paquetes de venta</h2>
                                    <p>El empaque se suma por paquete completo. Publico sugerido: 1.8x; familiar: 1.5x.</p>
                                </div>
                            </div>
                            <div class="package-table" data-package-list>
                                @foreach ($packageRows as $index => $package)
                                    <div class="package-row" data-package-row>
                                        <input class="form-control" name="sale_packages[{{ $index }}][name]" value="{{ $package['name'] ?? '' }}" placeholder="Nombre">
                                        <input class="form-control" name="sale_packages[{{ $index }}][quantity]" type="number" min="1" value="{{ $package['quantity'] ?? 1 }}" placeholder="Piezas" data-package-quantity>
                                        <input class="form-control" name="sale_packages[{{ $index }}][packaging_cost]" type="number" min="0" step="0.01" value="{{ $package['packaging_cost'] ?? 0 }}" placeholder="Empaque" data-package-packaging>
                                        <input class="form-control" name="sale_packages[{{ $index }}][family_multiplier]" type="number" min="1" step="0.01" value="{{ $package['family_multiplier'] ?? 1.5 }}" placeholder="Fam." data-package-family-multiplier>
                                        <input class="form-control" name="sale_packages[{{ $index }}][public_multiplier]" type="number" min="1" step="0.01" value="{{ $package['public_multiplier'] ?? 1.8 }}" placeholder="Publico" data-package-public-multiplier>
                                        <label class="default-package-check">
                                            <input class="form-check-input" name="sale_packages[{{ $index }}][is_default]" type="radio" value="1" @checked(! empty($package['is_default']))>
                                            <span>Principal</span>
                                        </label>
                                        <div class="package-result">
                                            <strong data-package-public-price>$0.00</strong>
                                            <small data-package-family-price>Familiar $0.00</small>
                                            <small data-package-cost>Costo $0.00</small>
                                            <small data-package-profit>Ganancia pub. $0.00</small>
                                            <small data-package-unit>Unitario $0.00</small>
                                        </div>
                                        <button class="btn btn-outline-danger" type="button" data-remove-row><i class="bi bi-x-lg"></i></button>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-outline-dark btn-sm mt-3" type="button" data-add-package>
                                <i class="bi bi-plus-circle me-2"></i>Agregar paquete
                            </button>
                        </div>

                        <div class="panel-card mb-4" data-gallery-fields>
                            <div class="form-section-title">
                                <i class="bi bi-images"></i>
                                <div>
                                    <h2>Galeria del producto</h2>
                                    <p>Sube varias fotos. El cliente las vera en carrusel.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label" for="stock">Stock del producto</label>
                                    <input class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock ?? 0) }}" data-gallery-stock>
                                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label" for="gallery_photos">Fotos de galeria</label>
                                    <input class="form-control @error('gallery_photos.*') is-invalid @enderror" id="gallery_photos" name="gallery_photos[]" type="file" accept="image/*" multiple>
                                    @error('gallery_photos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($product->photos->isNotEmpty())
                                        <div class="form-text">{{ $product->photos->count() }} foto(s) cargadas.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="panel-card" data-customization-fields>
                            <div class="form-section-title">
                                <i class="bi bi-palette-fill"></i>
                                <div>
                                    <h2>Opciones del producto</h2>
                                    <p>Tipo/color cambia la imagen base; diseno se coloca encima de esa imagen en el single.</p>
                                </div>
                            </div>
                            <div class="option-table" data-option-list>
                                @foreach ($optionRows as $index => $option)
                                    <div class="option-row" data-option-row>
                                        <select class="form-select" name="options[{{ $index }}][group]" data-option-group>
                                            @foreach ($optionGroups as $value => $label)
                                                <option value="{{ $value }}" @selected(($option['group'] ?? 'tipo') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <input class="form-control" name="options[{{ $index }}][name]" value="{{ $option['name'] ?? '' }}" placeholder="Nombre de opcion">
                                        <input class="form-control" name="options[{{ $index }}][stock]" type="number" min="0" value="{{ $option['stock'] ?? 0 }}" placeholder="Stock color" data-color-stock>
                                        <input class="form-control" name="options[{{ $index }}][image]" type="file" accept="image/*">
                                        <input name="options[{{ $index }}][existing_image_path]" type="hidden" value="{{ $option['existing_image_path'] ?? '' }}">
                                        <button class="btn btn-outline-danger" type="button" data-remove-row><i class="bi bi-x-lg"></i></button>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-outline-dark btn-sm mt-3" type="button" data-add-option>
                                <i class="bi bi-plus-circle me-2"></i>Agregar opcion
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="panel-card sticky-copy">
                            <div class="form-section-title compact">
                                <i class="bi bi-calculator-fill"></i>
                                <div>
                                    <h2>Precio calculado</h2>
                                    <p>Base sin empaque. Los paquetes ya suman empaque por juego.</p>
                                </div>
                            </div>
                            <div class="price-summary">
                                <div><span>Subtotal</span><strong data-subtotal>$0.00</strong></div>
                                <div><span>Familia y amigos</span><strong data-friends-price>$0.00</strong><small data-friends-profit>Ganancia $0.00</small></div>
                                <div><span>Precio publico</span><strong data-public-price>$0.00</strong><small data-public-profit>Ganancia $0.00</small></div>
                            </div>
                            <div class="price-summary mt-4">
                                <div><span data-stock-summary-label>Stock total por colores</span><strong data-total-stock>{{ $product->stock ?? 0 }}</strong></div>
                            </div>
                            <div class="form-check form-switch mt-4 mb-3">
                                <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active))>
                                <label class="form-check-label" for="is_active">Activo en pagina</label>
                            </div>
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" id="is_featured" name="is_featured" type="checkbox" value="1" @checked(old('is_featured', $product->is_featured))>
                                <label class="form-check-label" for="is_featured">Destacado ancho</label>
                            </div>
                            <button class="btn btn-dark w-100" type="submit">
                                <i class="bi bi-save me-2"></i>{{ $isEdit ? 'Guardar producto' : 'Crear producto' }}
                            </button>
                            @if ($isEdit)
                                <a class="btn btn-outline-dark w-100 mt-2" href="{{ route('admin.catalog.preview', $product) }}">
                                    <i class="bi bi-eye me-2"></i>Vista previa
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <template data-cost-template>
        <div class="cost-row" data-cost-row>
            <input class="form-control" name="costs[__INDEX__][name]" placeholder="Insumo">
            <input class="form-control" name="costs[__INDEX__][cost]" type="number" min="0" step="0.01" placeholder="Costo" data-cost-input>
            <button class="btn btn-outline-danger" type="button" data-remove-row><i class="bi bi-x-lg"></i></button>
        </div>
    </template>

    <template data-option-template>
        <div class="option-row" data-option-row>
            <select class="form-select" name="options[__INDEX__][group]" data-option-group>
                @foreach ($optionGroups as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <input class="form-control" name="options[__INDEX__][name]" placeholder="Nombre de opcion">
            <input class="form-control" name="options[__INDEX__][stock]" type="number" min="0" value="0" placeholder="Stock color" data-color-stock>
            <input class="form-control" name="options[__INDEX__][image]" type="file" accept="image/*">
            <input name="options[__INDEX__][existing_image_path]" type="hidden">
            <button class="btn btn-outline-danger" type="button" data-remove-row><i class="bi bi-x-lg"></i></button>
        </div>
    </template>

    <template data-package-template>
        <div class="package-row" data-package-row>
            <input class="form-control" name="sale_packages[__INDEX__][name]" placeholder="Nombre">
            <input class="form-control" name="sale_packages[__INDEX__][quantity]" type="number" min="1" value="1" placeholder="Piezas" data-package-quantity>
            <input class="form-control" name="sale_packages[__INDEX__][packaging_cost]" type="number" min="0" step="0.01" value="0" placeholder="Empaque" data-package-packaging>
            <input class="form-control" name="sale_packages[__INDEX__][family_multiplier]" type="number" min="1" step="0.01" value="1.5" placeholder="Fam." data-package-family-multiplier>
            <input class="form-control" name="sale_packages[__INDEX__][public_multiplier]" type="number" min="1" step="0.01" value="1.8" placeholder="Publico" data-package-public-multiplier>
            <label class="default-package-check">
                <input class="form-check-input" name="sale_packages[__INDEX__][is_default]" type="radio" value="1">
                <span>Principal</span>
            </label>
            <div class="package-result">
                <strong data-package-public-price>$0.00</strong>
                <small data-package-family-price>Familiar $0.00</small>
                <small data-package-cost>Costo $0.00</small>
                <small data-package-profit>Ganancia pub. $0.00</small>
                <small data-package-unit>Unitario $0.00</small>
            </div>
            <button class="btn btn-outline-danger" type="button" data-remove-row><i class="bi bi-x-lg"></i></button>
        </div>
    </template>

    <script>
        (() => {
            const form = document.querySelector('[data-product-form]');
            if (!form) return;

            const money = (value) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
            const costList = form.querySelector('[data-cost-list]');
            const optionList = form.querySelector('[data-option-list]');
            const costTemplate = document.querySelector('[data-cost-template]').innerHTML;
            const optionTemplate = document.querySelector('[data-option-template]').innerHTML;
            const packageList = form.querySelector('[data-package-list]');
            const packageTemplate = document.querySelector('[data-package-template]').innerHTML;
            const modeSelect = form.querySelector('[name="presentation_mode"]');
            const galleryFields = form.querySelector('[data-gallery-fields]');
            const customizationFields = form.querySelector('[data-customization-fields]');
            const galleryStock = form.querySelector('[data-gallery-stock]');
            const stockSummaryLabel = form.querySelector('[data-stock-summary-label]');

            const calculate = () => {
                const isGallery = modeSelect?.value === 'gallery';
                const subtotal = [...form.querySelectorAll('[data-cost-input]')]
                    .reduce((sum, input) => sum + (Number.parseFloat(input.value) || 0), 0);
                const colorStock = [...form.querySelectorAll('[data-option-row]')]
                    .filter((row) => row.querySelector('[data-option-group]')?.value === 'color')
                    .reduce((sum, row) => sum + (Number.parseInt(row.querySelector('[data-color-stock]')?.value, 10) || 0), 0);
                const totalStock = isGallery ? (Number.parseInt(galleryStock?.value, 10) || 0) : colorStock;
                const friends = subtotal * 1.5;
                const publicPrice = subtotal * 1.8;

                form.querySelector('[data-subtotal]').textContent = money(subtotal);
                form.querySelector('[data-friends-price]').textContent = money(friends);
                form.querySelector('[data-friends-profit]').textContent = `Ganancia ${money(friends - subtotal)}`;
                form.querySelector('[data-public-price]').textContent = money(publicPrice);
                form.querySelector('[data-public-profit]').textContent = `Ganancia ${money(publicPrice - subtotal)}`;
                form.querySelector('[data-total-stock]').textContent = totalStock;
                if (stockSummaryLabel) {
                    stockSummaryLabel.textContent = isGallery ? 'Stock del producto' : 'Stock total por colores';
                }
                if (galleryFields) {
                    galleryFields.hidden = !isGallery;
                }
                if (customizationFields) {
                    customizationFields.hidden = isGallery;
                }
                form.querySelectorAll('[data-package-row]').forEach((row) => {
                    const quantity = Math.max(1, Number.parseInt(row.querySelector('[data-package-quantity]')?.value, 10) || 1);
                    const packaging = Math.max(0, Number.parseFloat(row.querySelector('[data-package-packaging]')?.value) || 0);
                    const familyMultiplier = Math.max(1, Number.parseFloat(row.querySelector('[data-package-family-multiplier]')?.value) || 1);
                    const publicMultiplier = Math.max(1, Number.parseFloat(row.querySelector('[data-package-public-multiplier]')?.value) || 1);
                    const packageCost = (subtotal * quantity) + packaging;
                    const familyPrice = packageCost * familyMultiplier;
                    const publicPackagePrice = packageCost * publicMultiplier;

                    row.querySelector('[data-package-public-price]').textContent = money(publicPackagePrice);
                    row.querySelector('[data-package-family-price]').textContent = `Familiar ${money(familyPrice)}`;
                    row.querySelector('[data-package-cost]').textContent = `Costo ${money(packageCost)} con empaque`;
                    row.querySelector('[data-package-profit]').textContent = `Ganancia pub. ${money(publicPackagePrice - packageCost)}`;
                    row.querySelector('[data-package-unit]').textContent = `Unitario pub. ${money(publicPackagePrice / quantity)}`;
                });
                form.querySelectorAll('[data-option-row]').forEach((row) => {
                    const isColor = row.querySelector('[data-option-group]')?.value === 'color';
                    const stock = row.querySelector('[data-color-stock]');
                    if (stock) {
                        stock.disabled = !isColor;
                        stock.closest('.form-control')?.classList.toggle('d-none', !isColor);
                    }
                });
            };

            form.addEventListener('input', (event) => {
                if (event.target.matches('[data-cost-input], [data-color-stock], [data-gallery-stock], [data-package-quantity], [data-package-packaging], [data-package-family-multiplier], [data-package-public-multiplier]')) {
                    calculate();
                }
            });

            form.addEventListener('change', (event) => {
                if (event.target.matches('[data-option-group], [name="presentation_mode"]')) {
                    calculate();
                }
            });

            form.addEventListener('click', (event) => {
                const remove = event.target.closest('[data-remove-row]');
                if (remove) {
                    remove.closest('[data-cost-row], [data-option-row], [data-package-row]')?.remove();
                    calculate();
                }
            });

            form.querySelector('[data-add-cost]')?.addEventListener('click', () => {
                costList.insertAdjacentHTML('beforeend', costTemplate.replaceAll('__INDEX__', Date.now()));
            });

            form.querySelector('[data-add-option]')?.addEventListener('click', () => {
                optionList.insertAdjacentHTML('beforeend', optionTemplate.replaceAll('__INDEX__', Date.now()));
            });

            form.querySelector('[data-add-package]')?.addEventListener('click', () => {
                packageList.insertAdjacentHTML('beforeend', packageTemplate.replaceAll('__INDEX__', Date.now()));
                calculate();
            });

            calculate();
        })();
    </script>
@endsection
