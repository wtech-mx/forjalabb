@extends('layouts.app')

@section('title', 'Vaso cafe ceramica | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow">Mini catalogo de temporada</div>
                    <h1 class="fw-bold mt-2 mb-0">Vaso cafe ceramica</h1>
                </div>
                <p class="text-secondary mb-0 max-copy">Vaso blanco tipo cafe con interior y tapa de color. Elige color y prueba los disenos oficiales para armar piezas de temporada.</p>
            </div>

            <article class="tequila-feature drinkware-feature" data-drinkware-configurator data-drinkware-product="vaso cafe ceramica">
                <div class="tequila-photo drinkware-photo">
                    <div class="drinkware-preview photo-drinkware-preview travel-photo-preview" data-drinkware-preview data-color="pink">
                        <div class="drinkware-photo-stage">
                            <img class="drinkware-base-photo" src="{{ asset('images/catalog/vaso-cafe-producto-studio.png') }}" alt="Vaso cafe ceramica blanco con interior y tapa de color">
                            <img class="drinkware-design-on-photo" src="{{ asset('images/catalog/aguacate-teq-transparent.png') }}" alt="" data-drinkware-preview-design>
                        </div>
                        <div class="tequila-shadow drinkware-shadow" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="tequila-copy">
                    <span class="badge text-bg-success mb-3">Vista interactiva</span>
                    <h2>Arma tu vaso</h2>
                    <p>Selecciona el color y el diseno. La vista muestra exterior blanco con interior y tapa a juego para imaginar el resultado final.</p>

                    <div class="color-list" role="group" aria-label="Color de vaso cafe">
                        @foreach ([
                            ['key' => 'green', 'name' => 'Verde', 'hex' => '#82c341'],
                            ['key' => 'red', 'name' => 'Rojo', 'hex' => '#ee3848'],
                            ['key' => 'blue', 'name' => 'Azul', 'hex' => '#313083'],
                            ['key' => 'pink', 'name' => 'Rosa', 'hex' => '#e9a1be'],
                        ] as $index => $color)
                            <button class="{{ $color['key'] === 'pink' ? 'active' : '' }}" type="button" data-drinkware-color="{{ $color['key'] }}" data-drinkware-color-name="{{ $color['name'] }}">
                                <span style="--swatch: {{ $color['hex'] }}"></span>{{ $color['name'] }}
                            </button>
                        @endforeach
                    </div>

                    <div class="design-strip" aria-label="Disenos oficiales para vaso cafe">
                        @foreach ([
                            ['file' => 'aguacate-teq-transparent.png', 'name' => 'Aguacate'],
                            ['file' => 'nopal-teq-transparent.png', 'name' => 'Nopal'],
                            ['file' => 'chile-teq-transparent.png', 'name' => 'Chile'],
                            ['file' => 'pastor-teq-transparent.png', 'name' => 'Pastor'],
                            ['file' => 'elote-teq-transparent.png', 'name' => 'Elote'],
                            ['file' => 'botella-teq-transparent.png', 'name' => 'Botella'],
                        ] as $index => $design)
                            <figure class="{{ $index === 0 ? 'active' : '' }}" data-drinkware-design="{{ asset('images/catalog/'.$design['file']) }}" data-drinkware-name="{{ $design['name'] }}">
                                <img src="{{ asset('images/catalog/'.$design['file']) }}" alt="Diseno de {{ $design['name'] }} para vaso cafe ceramica">
                                <figcaption>{{ $design['name'] }}</figcaption>
                            </figure>
                        @endforeach
                    </div>

                    <p class="tequila-selection" data-drinkware-selection>Vista: Vaso cafe ceramica rosa con diseno Aguacate</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-dark" data-drinkware-quote href="https://wa.me/525564442949?text=Hola%2C%20quiero%20cotizar%20vaso%20cafe%20ceramica%20personalizado.%20Me%20interesa%20color%20Rosa%20con%20diseno%20Aguacate." target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar vaso
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
