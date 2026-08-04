@extends('layouts.app')

@php
    $baseOptions = $product->options->whereIn('group', ['tipo', 'color'])->values();
    $designOptions = $product->options->where('group', 'diseno')->values();
    $initialBase = $baseOptions->first();
    $initialDesign = $designOptions->first();
    $baseImage = $initialBase?->image_url ?: $product->image_url;
@endphp

@section('title', $product->name.' | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            <article class="tequila-feature dynamic-product" data-dynamic-product>
                <div class="tequila-photo">
                    <div class="dynamic-product-preview">
                        @if ($baseImage)
                            <img class="dynamic-product-base" src="{{ $baseImage }}" alt="{{ $product->name }}" data-base-preview>
                        @else
                            <div class="catalog-media"><i class="bi bi-box-seam"></i><span>Foto pendiente</span></div>
                        @endif
                        @if ($initialDesign?->image_url)
                            <img class="dynamic-product-design" src="{{ $initialDesign->image_url }}" alt="" data-design-preview>
                        @else
                            <img class="dynamic-product-design" alt="" data-design-preview hidden>
                        @endif
                    </div>
                </div>
                <div class="tequila-copy">
                    <span class="badge text-bg-success mb-3">Vista interactiva</span>
                    <h1>{{ $product->name }}</h1>
                    <p>{{ $product->description }}</p>
                    @if ($product->public_price > 0)
                        <div class="package-price">${{ number_format((float) $product->public_price, 2) }}</div>
                    @endif
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-light" data-stock-label>
                            Stock: {{ $initialBase?->group === 'color' ? $initialBase->stock : $product->stock }}
                        </span>
                        <span class="badge text-bg-light">{{ \App\Models\CatalogProduct::PRESENTATION_MODES[$product->presentation_mode] ?? $product->presentation_mode }}</span>
                    </div>
                    @if ($baseOptions->isNotEmpty())
                        <div class="finish-list mb-3" role="group" aria-label="Opciones de tipo o color">
                            @foreach ($baseOptions as $index => $option)
                                <button class="{{ $index === 0 ? 'active' : '' }}" type="button" data-base-option="{{ $option->image_url ?: $product->image_url }}" data-base-name="{{ $option->name }}" data-base-stock="{{ $option->group === 'color' ? $option->stock : $product->stock }}">
                                    {{ $option->name }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @if ($designOptions->isNotEmpty())
                        <div>
                            <div class="design-strip mb-4">
                                @foreach ($designOptions as $index => $option)
                                    <figure class="{{ $index === 0 ? 'active' : '' }}" data-design-option="{{ $option->image_url }}" data-design-name="{{ $option->name }}">
                                        @if ($option->image_url)
                                            <img src="{{ $option->image_url }}" alt="{{ $option->name }}">
                                        @else
                                            <i class="bi bi-palette-fill"></i>
                                        @endif
                                        <figcaption>{{ $option->name }}</figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($product->salePackages->isNotEmpty())
                        <div class="sale-package-list mb-4">
                            @foreach ($product->salePackages as $package)
                                <div class="{{ $package->is_default ? 'active' : '' }}">
                                    <span>{{ $package->name }}</span>
                                    <strong>${{ number_format((float) $package->public_price, 2) }}</strong>
                                    <small>{{ $package->quantity }} pieza{{ $package->quantity === 1 ? '' : 's' }} · ${{ number_format((float) $package->unit_public_price, 2) }} c/u · empaque incluido</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <p class="tequila-selection" data-product-selection>
                        Vista: {{ $initialBase?->name ?: 'Base' }}{{ $initialDesign ? ' con diseno '.$initialDesign->name : '' }}
                    </p>
                    <a class="btn btn-dark btn-lg" href="https://wa.me/?text={{ rawurlencode('Hola, quiero cotizar '.$product->name) }}" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp me-2"></i>Cotizar
                    </a>
                </div>
            </article>
        </div>
    </section>

    <script>
        (() => {
            document.querySelectorAll('[data-dynamic-product]').forEach((product) => {
                const basePreview = product.querySelector('[data-base-preview]');
                const designPreview = product.querySelector('[data-design-preview]');
                const selection = product.querySelector('[data-product-selection]');
                const stockLabel = product.querySelector('[data-stock-label]');
                let baseName = product.querySelector('[data-base-option].active')?.dataset.baseName || 'Base';
                let designName = product.querySelector('[data-design-option].active')?.dataset.designName || '';

                const updateSelection = () => {
                    if (selection) {
                        selection.textContent = `Vista: ${baseName}${designName ? ` con diseno ${designName}` : ''}`;
                    }
                };

                product.querySelectorAll('[data-base-option]').forEach((button) => {
                    button.addEventListener('click', () => {
                        product.querySelectorAll('[data-base-option]').forEach((item) => item.classList.toggle('active', item === button));
                        baseName = button.dataset.baseName || 'Base';
                        if (basePreview && button.dataset.baseOption) {
                            basePreview.src = button.dataset.baseOption;
                        }
                        if (stockLabel) {
                            stockLabel.textContent = `Stock: ${button.dataset.baseStock || 0}`;
                        }
                        updateSelection();
                    });
                });

                product.querySelectorAll('[data-design-option]').forEach((figure) => {
                    figure.addEventListener('click', () => {
                        product.querySelectorAll('[data-design-option]').forEach((item) => item.classList.toggle('active', item === figure));
                        designName = figure.dataset.designName || '';
                        if (designPreview && figure.dataset.designOption) {
                            designPreview.src = figure.dataset.designOption;
                            designPreview.hidden = false;
                        }
                        updateSelection();
                    });
                });
            });
        })();
    </script>
@endsection
