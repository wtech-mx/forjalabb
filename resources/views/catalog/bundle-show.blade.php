@extends('layouts.app')

@php
    $initialImage = $bundle->image_url ?: $bundle->items->first()?->product?->image_url;
@endphp

@section('title', $bundle->name.' | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            <article class="bundle-single" data-bundle-single>
                <div class="bundle-single-media">
                    @if ($initialImage)
                        <img src="{{ $initialImage }}" alt="{{ $bundle->name }}" data-bundle-preview>
                    @else
                        <div class="catalog-media"><i class="bi bi-box-seam"></i><span>Foto pendiente</span></div>
                    @endif
                </div>

                <div class="bundle-single-copy">
                    <div class="eyebrow">Paquete armado</div>
                    <h1>{{ $bundle->name }}</h1>
                    @if ($bundle->description)
                        <p>{{ $bundle->description }}</p>
                    @endif

                    @if ($bundle->items->isNotEmpty())
                        <div class="bundle-item-picker" role="group" aria-label="Productos incluidos">
                            @foreach ($bundle->items as $index => $item)
                                @php
                                    $product = $item->product;
                                    $image = $product?->image_url ?: $bundle->image_url;
                                @endphp
                                <button class="{{ $index === 0 ? 'active' : '' }}" type="button" data-bundle-item-option data-image="{{ $image }}" data-name="{{ $product?->name }}">
                                    <span>{{ $item->quantity }}x</span>
                                    <strong>{{ $product?->name }}</strong>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @if ($bundle->photos->isNotEmpty())
                        <div class="bundle-single-gallery">
                            @foreach ($bundle->photos as $photo)
                                <button type="button" data-bundle-item-option data-image="{{ $photo->image_url }}" data-name="{{ $bundle->name }}">
                                    <img src="{{ $photo->image_url }}" alt="{{ $bundle->name }}">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="bundle-single-summary">
                        <span data-bundle-selection>{{ $bundle->items->first()?->product?->name ?: $bundle->name }}</span>
                        @if ($bundle->public_price > 0)
                            <strong>${{ number_format((float) $bundle->public_price, 2) }}</strong>
                        @endif
                    </div>

                    <a class="btn btn-dark btn-lg" href="https://wa.me/?text={{ rawurlencode('Hola, quiero cotizar el paquete '.$bundle->name) }}" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp me-2"></i>Cotizar paquete
                    </a>
                </div>
            </article>
        </div>
    </section>

    <script>
        (() => {
            document.querySelectorAll('[data-bundle-single]').forEach((bundle) => {
                const preview = bundle.querySelector('[data-bundle-preview]');
                const selection = bundle.querySelector('[data-bundle-selection]');

                bundle.querySelectorAll('[data-bundle-item-option]').forEach((button) => {
                    button.addEventListener('click', () => {
                        bundle.querySelectorAll('[data-bundle-item-option]').forEach((item) => item.classList.toggle('active', item === button));
                        if (preview && button.dataset.image) {
                            preview.src = button.dataset.image;
                        }
                        if (selection && button.dataset.name) {
                            selection.textContent = button.dataset.name;
                        }
                    });
                });
            });
        })();
    </script>
@endsection
