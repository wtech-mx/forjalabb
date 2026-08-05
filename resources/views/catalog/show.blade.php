@extends('layouts.app')

@php
    $isGallery = $product->presentation_mode === \App\Models\CatalogProduct::MODE_GALLERY;
    $baseOptions = $product->options->whereIn('group', ['tipo', 'color'])->values();
    $designOptions = $product->options->where('group', 'diseno')->values();
    $initialBase = $baseOptions->first();
    $initialDesign = $designOptions->first();
    $initialPackage = $product->salePackages->firstWhere('is_default', true) ?: $product->salePackages->first();
    $baseImage = $initialBase?->image_url ?: $product->image_url;
    $galleryImages = collect([$product->image_url])
        ->merge($product->photos->map->image_url)
        ->filter()
        ->values();
@endphp

@section('title', $product->name.' | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            @if ($isGallery)
                <article class="product-gallery-single" data-gallery-product>
                    <div class="mobile-product-heading product-gallery-mobile-heading">
                        <h1>{{ $product->name }}</h1>
                    </div>
                    <div class="product-gallery-media">
                        @if ($galleryImages->isNotEmpty())
                            <img src="{{ $galleryImages->first() }}" alt="{{ $product->name }}" data-gallery-preview>
                        @else
                            <div class="catalog-media"><i class="bi bi-box-seam"></i><span>Foto pendiente</span></div>
                        @endif
                    </div>
                    <div class="product-gallery-copy">
                        <div class="eyebrow">Galeria de producto</div>
                        <h1>{{ $product->name }}</h1>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge text-bg-light">Stock: {{ $product->stock }}</span>
                            <span class="badge text-bg-light">Galeria de fotos</span>
                        </div>
                        @if ($galleryImages->count() > 1)
                            <div class="product-gallery-strip">
                                @foreach ($galleryImages as $index => $image)
                                    <button class="{{ $index === 0 ? 'active' : '' }}" type="button" data-gallery-thumb="{{ $image }}">
                                        <img src="{{ $image }}" alt="{{ $product->name }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <p>{{ $product->description }}</p>
                        @if ($product->salePackages->isNotEmpty())
                            <div class="sale-package-list sale-package-picker mb-2" role="group" aria-label="Paquetes disponibles">
                                @foreach ($product->salePackages as $package)
                                    <button class="{{ $package->is_default ? 'active' : '' }}" type="button" data-package-option data-package-name="{{ $package->name }}" data-package-price="{{ number_format((float) $package->public_price, 2, '.', '') }}">
                                        <span>{{ $package->name }}</span>
                                        <strong>${{ number_format((float) $package->public_price, 2) }}</strong>
                                        <small>{{ $package->quantity }} pieza{{ $package->quantity === 1 ? '' : 's' }} &middot; ${{ number_format((float) $package->unit_public_price, 2) }} c/u</small>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @if (($initialPackage?->public_price ?? $product->public_price) > 0)
                            <div class="package-price package-price-final" data-price-label>${{ number_format((float) ($initialPackage?->public_price ?? $product->public_price), 2) }}</div>
                        @endif
                        <a class="btn btn-dark btn-lg" href="https://wa.me/?text={{ rawurlencode('Hola, quiero cotizar '.$product->name.($initialPackage ? ' en '.$initialPackage->name : '')) }}" target="_blank" rel="noopener" data-whatsapp-link data-product-name="{{ $product->name }}">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar
                        </a>
                    </div>
                </article>
            @else
                <article class="tequila-feature dynamic-product" data-dynamic-product>
                    <div class="mobile-product-heading">
                        <h1>{{ $product->name }}</h1>
                    </div>

                    <div class="tequila-photo">
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

                    <div class="tequila-copy">
                        <h1>{{ $product->name }}</h1>
                        <p>{{ $product->description }}</p>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge text-bg-light" data-stock-label>
                                Stock: {{ $initialBase?->group === 'color' ? $initialBase->stock : $product->stock }}
                            </span>
                            <span class="badge text-bg-light">{{ \App\Models\CatalogProduct::PRESENTATION_MODES[$product->presentation_mode] ?? $product->presentation_mode }}</span>
                        </div>

                        @if ($designOptions->isNotEmpty())
                            <div class="design-options-panel">
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

                        @if ($baseOptions->isNotEmpty())
                            <div class="finish-list mb-3" role="group" aria-label="Opciones de tipo o color">
                                @foreach ($baseOptions as $index => $option)
                                    <button class="{{ $index === 0 ? 'active' : '' }}" type="button" data-base-option="{{ $option->image_url ?: $product->image_url }}" data-base-name="{{ $option->name }}" data-base-stock="{{ $option->group === 'color' ? $option->stock : $product->stock }}">
                                        {{ $option->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if ($product->salePackages->isNotEmpty())
                            <div class="sale-package-list sale-package-picker mb-4" role="group" aria-label="Paquetes disponibles">
                                @foreach ($product->salePackages as $package)
                                    <button class="{{ $package->is_default ? 'active' : '' }}" type="button" data-package-option data-package-name="{{ $package->name }}" data-package-price="{{ number_format((float) $package->public_price, 2, '.', '') }}">
                                        <span>{{ $package->name }}</span>
                                        <strong>${{ number_format((float) $package->public_price, 2) }}</strong>
                                        <small>{{ $package->quantity }} pieza{{ $package->quantity === 1 ? '' : 's' }} &middot; ${{ number_format((float) $package->unit_public_price, 2) }} c/u &middot; empaque incluido</small>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <p class="tequila-selection" data-product-selection>
                            Vista: {{ $initialBase?->name ?: 'Base' }}{{ $initialDesign ? ' con diseno '.$initialDesign->name : '' }}{{ $initialPackage ? ' - '.$initialPackage->name : '' }}
                        </p>

                        @if (($initialPackage?->public_price ?? $product->public_price) > 0)
                            <div class="package-price package-price-final" data-price-label>${{ number_format((float) ($initialPackage?->public_price ?? $product->public_price), 2) }}</div>
                        @endif

                        <a class="btn btn-dark btn-lg" href="https://wa.me/?text={{ rawurlencode('Hola, quiero cotizar '.$product->name.($initialPackage ? ' en '.$initialPackage->name : '')) }}" target="_blank" rel="noopener" data-whatsapp-link data-product-name="{{ $product->name }}">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar
                        </a>
                    </div>
                </article>
            @endif
        </div>
    </section>

    <script>
        (() => {
            document.querySelectorAll('[data-gallery-product]').forEach((product) => {
                const preview = product.querySelector('[data-gallery-preview]');
                const priceLabel = product.querySelector('[data-price-label]');
                const whatsappLink = product.querySelector('[data-whatsapp-link]');
                let packageName = product.querySelector('[data-package-option].active')?.dataset.packageName || '';

                const updateWhatsapp = () => {
                    if (!whatsappLink) return;
                    const productName = whatsappLink.dataset.productName || '';
                    whatsappLink.href = `https://wa.me/?text=${encodeURIComponent(`Hola, quiero cotizar ${productName}${packageName ? ` en ${packageName}` : ''}`)}`;
                };

                product.querySelectorAll('[data-gallery-thumb]').forEach((button) => {
                    button.addEventListener('click', () => {
                        product.querySelectorAll('[data-gallery-thumb]').forEach((item) => item.classList.toggle('active', item === button));
                        if (preview && button.dataset.galleryThumb) {
                            preview.src = button.dataset.galleryThumb;
                        }
                    });
                });

                product.querySelectorAll('[data-package-option]').forEach((button) => {
                    button.addEventListener('click', () => {
                        product.querySelectorAll('[data-package-option]').forEach((item) => item.classList.toggle('active', item === button));
                        packageName = button.dataset.packageName || '';
                        if (priceLabel && button.dataset.packagePrice) {
                            const amount = Number.parseFloat(button.dataset.packagePrice) || 0;
                            priceLabel.textContent = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);
                        }
                        updateWhatsapp();
                    });
                });
            });

            document.querySelectorAll('[data-dynamic-product]').forEach((product) => {
                const basePreview = product.querySelector('[data-base-preview]');
                const designPreview = product.querySelector('[data-design-preview]');
                const selection = product.querySelector('[data-product-selection]');
                const stockLabel = product.querySelector('[data-stock-label]');
                const priceLabel = product.querySelector('[data-price-label]');
                const whatsappLink = product.querySelector('[data-whatsapp-link]');
                let baseName = product.querySelector('[data-base-option].active')?.dataset.baseName || 'Base';
                let designName = product.querySelector('[data-design-option].active')?.dataset.designName || '';
                let packageName = product.querySelector('[data-package-option].active')?.dataset.packageName || '';

                const updateSelection = () => {
                    if (selection) {
                        selection.textContent = `Vista: ${baseName}${designName ? ` con diseno ${designName}` : ''}${packageName ? ` - ${packageName}` : ''}`;
                    }

                    if (whatsappLink) {
                        const productName = whatsappLink.dataset.productName || '';
                        const text = `Hola, quiero cotizar ${productName}${packageName ? ` en ${packageName}` : ''}${baseName ? `, ${baseName}` : ''}${designName ? ` con diseno ${designName}` : ''}`;
                        whatsappLink.href = `https://wa.me/?text=${encodeURIComponent(text)}`;
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

                product.querySelectorAll('[data-package-option]').forEach((button) => {
                    button.addEventListener('click', () => {
                        product.querySelectorAll('[data-package-option]').forEach((item) => item.classList.toggle('active', item === button));
                        packageName = button.dataset.packageName || '';
                        if (priceLabel && button.dataset.packagePrice) {
                            const amount = Number.parseFloat(button.dataset.packagePrice) || 0;
                            priceLabel.textContent = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);
                        }
                        updateSelection();
                    });
                });
            });
        })();
    </script>
@endsection
