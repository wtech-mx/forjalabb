@extends('layouts.app')

@section('title', 'Termo color mate | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow">Mini catalogo de temporada</div>
                    <h1 class="fw-bold mt-2 mb-0">Termo color mate</h1>
                </div>
                <p class="text-secondary mb-0 max-copy">Termos de acabado mate en negro, gris, verde o blanco. Elige el color para visualizarlo antes de cotizar.</p>
            </div>

            <article class="tequila-feature drinkware-feature" data-color-product-configurator data-color-product="termo color mate">
                <div class="tequila-photo thermo-photo">
                    <div class="drinkware-preview photo-drinkware-preview thermo-photo-preview" data-color-product-preview data-color="white">
                        <div class="drinkware-photo-stage">
                            <img class="drinkware-base-photo" src="{{ asset('images/catalog/termo-mate-producto-studio.png') }}" alt="Termo color mate">
                        </div>
                        <div class="tequila-shadow drinkware-shadow" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="tequila-copy">
                    <span class="badge text-bg-success mb-3">Vista interactiva</span>
                    <h2>Elige tu termo</h2>
                    <p>Selecciona el color mate disponible. La vista usa la misma base para que compares el acabado sin distracciones.</p>

                    <div class="color-list" role="group" aria-label="Color de termo">
                        @foreach ([
                            ['key' => 'black', 'name' => 'Negro', 'hex' => '#141414'],
                            ['key' => 'gray', 'name' => 'Gris', 'hex' => '#73808a'],
                            ['key' => 'forest', 'name' => 'Verde', 'hex' => '#1f5847'],
                            ['key' => 'white', 'name' => 'Blanco', 'hex' => '#f4f6f4'],
                        ] as $index => $color)
                            <button class="{{ $color['key'] === 'white' ? 'active' : '' }}" type="button" data-color-product-color="{{ $color['key'] }}" data-color-product-color-name="{{ $color['name'] }}">
                                <span style="--swatch: {{ $color['hex'] }}"></span>{{ $color['name'] }}
                            </button>
                        @endforeach
                    </div>

                    <p class="tequila-selection" data-color-product-selection>Vista: Termo color mate blanco</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-dark" data-color-product-quote href="https://wa.me/?text=Hola%2C%20quiero%20cotizar%20termo%20color%20mate.%20Me%20interesa%20color%20Blanco." target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar termo
                        </a>
                        <a class="btn btn-outline-dark" href="{{ route('home') }}#catalogo">
                            <i class="bi bi-arrow-left me-2"></i>Volver al catalogo
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
