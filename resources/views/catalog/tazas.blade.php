@extends('layouts.app')

@section('title', 'Tazas personalizadas | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow">Mini catalogo de temporada</div>
                    <h1 class="fw-bold mt-2 mb-0">Tazas personalizadas</h1>
                </div>
                <p class="text-secondary mb-0 max-copy">Taza blanca con color interior y asa a juego. Elige color y prueba los disenos oficiales de temporada patria antes de cotizar.</p>
            </div>

            <article class="tequila-feature drinkware-feature" data-drinkware-configurator data-drinkware-product="taza">
                <div class="tequila-photo drinkware-photo">
                    <div class="drinkware-preview photo-drinkware-preview mug-photo-preview" data-drinkware-preview data-color="green">
                        <div class="drinkware-photo-stage">
                            <img class="drinkware-base-photo" src="{{ asset('images/catalog/taza-producto-studio.png') }}" alt="Taza blanca con interior y asa de color">
                            <img class="drinkware-design-on-photo" src="{{ asset('images/catalog/aguacate-teq-transparent.png') }}" alt="" data-drinkware-preview-design>
                        </div>
                        <div class="tequila-shadow drinkware-shadow" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="tequila-copy">
                    <span class="badge text-bg-success mb-3">Vista interactiva</span>
                    <h2>Arma tu taza</h2>
                    <p>Selecciona el color interior y el diseno. La taza mantiene exterior blanco y el color aparece en el interior y asa.</p>

                    <div class="color-list" role="group" aria-label="Color de taza">
                        @foreach ([
                            ['key' => 'green', 'name' => 'Verde', 'hex' => '#82c341'],
                            ['key' => 'red', 'name' => 'Rojo', 'hex' => '#ee3848'],
                            ['key' => 'blue', 'name' => 'Azul', 'hex' => '#313083'],
                            ['key' => 'pink', 'name' => 'Rosa', 'hex' => '#e9a1be'],
                        ] as $index => $color)
                            <button class="{{ $index === 0 ? 'active' : '' }}" type="button" data-drinkware-color="{{ $color['key'] }}" data-drinkware-color-name="{{ $color['name'] }}">
                                <span style="--swatch: {{ $color['hex'] }}"></span>{{ $color['name'] }}
                            </button>
                        @endforeach
                    </div>

                    <div class="design-strip" aria-label="Disenos oficiales para taza">
                        @foreach ([
                            ['file' => 'aguacate-teq-transparent.png', 'name' => 'Aguacate'],
                            ['file' => 'nopal-teq-transparent.png', 'name' => 'Nopal'],
                            ['file' => 'chile-teq-transparent.png', 'name' => 'Chile'],
                            ['file' => 'pastor-teq-transparent.png', 'name' => 'Pastor'],
                            ['file' => 'elote-teq-transparent.png', 'name' => 'Elote'],
                            ['file' => 'botella-teq-transparent.png', 'name' => 'Botella'],
                        ] as $index => $design)
                            <figure class="{{ $index === 0 ? 'active' : '' }}" data-drinkware-design="{{ asset('images/catalog/'.$design['file']) }}" data-drinkware-name="{{ $design['name'] }}">
                                <img src="{{ asset('images/catalog/'.$design['file']) }}" alt="Diseno de {{ $design['name'] }} para taza">
                                <figcaption>{{ $design['name'] }}</figcaption>
                            </figure>
                        @endforeach
                    </div>

                    <p class="tequila-selection" data-drinkware-selection>Vista: Taza verde con diseno Aguacate</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-dark" data-drinkware-quote href="https://wa.me/?text=Hola%2C%20quiero%20cotizar%20tazas%20personalizadas.%20Me%20interesa%20color%20Verde%20con%20diseno%20Aguacate." target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar tazas
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
