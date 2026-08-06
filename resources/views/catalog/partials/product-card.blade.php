@php
    $href = $product->resolved_url;
    $isExternal = str_starts_with($href, 'http');
@endphp

@if ($product->is_featured || $product->presentation === \App\Models\CatalogProduct::PRESENTATION_PACKAGE)
    <div class="season-package season-package-photo mb-4">
        <a class="season-package-media" href="{{ $href }}" @if($isExternal) target="_blank" rel="noopener" @endif>
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
            @else
                <div class="catalog-media"><i class="bi bi-{{ $product->icon ?: 'gift' }}"></i><span>Foto pendiente</span></div>
            @endif
        </a>
        <div>
            @if ($product->badge)
                <span class="badge text-bg-warning mb-3">{{ $product->badge }}</span>
            @endif
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            @if ($product->public_price > 0)
                <div class="package-price">${{ number_format((float) $product->public_price, 0) }}</div>
            @endif
            <div class="d-flex flex-wrap gap-2 mt-3">
                <a class="btn btn-dark" href="{{ $href }}" @if($isExternal) target="_blank" rel="noopener" @endif>
                    <i class="bi bi-arrow-right me-2"></i>{{ $product->action_label }}
                </a>
                <a class="btn btn-outline-light" href="https://wa.me/?text={{ rawurlencode('Hola, quiero cotizar '.$product->name) }}" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-2"></i>Cotizar
                </a>
            </div>
        </div>
    </div>
@else
    <a class="catalog-card catalog-card-link" href="{{ $href }}" @if($isExternal) target="_blank" rel="noopener" @endif>
        @if ($product->presentation === \App\Models\CatalogProduct::PRESENTATION_TEQUILA)
            <div class="catalog-media tequila-mini-media">
                <div class="mini-tequila-set" aria-hidden="true">
                    @foreach ([
                        'aguacate-teq-transparent.png',
                        'nopal-teq-transparent.png',
                        'chile-teq-transparent.png',
                        'pastor-teq-transparent.png',
                        'elote-teq-transparent.png',
                        'botella-teq-transparent.png',
                    ] as $file)
                        <span class="mini-tequila-shot">
                            <img src="{{ asset('images/catalog/'.$file) }}" alt="" loading="lazy" decoding="async">
                        </span>
                    @endforeach
                </div>
            </div>
        @elseif ($product->image_url)
            <div class="catalog-photo-media">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
            </div>
        @else
            <div class="catalog-media">
                <i class="bi bi-{{ $product->icon ?: 'box-seam' }}"></i>
                <span>Foto pendiente</span>
            </div>
        @endif
        <div class="catalog-body">
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            @if ($product->public_price > 0)
                <strong class="catalog-price">${{ number_format((float) $product->public_price, 0) }}</strong>
            @endif
            <span class="catalog-action">{{ $product->action_label }} <i class="bi bi-arrow-right"></i></span>
        </div>
    </a>
@endif
