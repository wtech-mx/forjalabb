@extends('layouts.app')

@section('title', 'Tequileros personalizados | ForjaLab')

@section('content')
    <section class="section-pad tequila-page">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow">Mini catalogo de temporada</div>
                    <h1 class="fw-bold mt-2 mb-0">Tequileros personalizados</h1>
                </div>
                <p class="text-secondary mb-0 max-copy">Elige acabado, prueba un diseno oficial y cotiza sets de 3 o 6 piezas para regalo, evento, negocio o temporada patria.</p>
            </div>

            <article class="tequila-feature" data-tequila-configurator>
                <div class="tequila-photo">
                    <div class="tequila-preview" data-tequila-preview data-finish="white">
                        <div class="tequila-shot" aria-hidden="true">
                            <img src="{{ asset('images/catalog/aguacate-teq-transparent.png') }}" alt="" data-tequila-preview-design>
                        </div>
                        <div class="tequila-shadow" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="tequila-copy">
                    <span class="badge text-bg-success mb-3">Vista interactiva</span>
                    <h2>Arma tu tequilero</h2>
                    <p>Selecciona el tipo de vaso y el diseno. La vista es una referencia rapida para imaginar el resultado antes de mandar a cotizar.</p>

                    <div class="finish-list" role="group" aria-label="Tipo de tequilero">
                        <button class="active" type="button" data-tequila-finish="white">Blanco</button>
                        <button type="button" data-tequila-finish="frosted">Satinado</button>
                        <button type="button" data-tequila-finish="clear">Transparente</button>
                    </div>

                    <div class="design-strip" aria-label="Disenos oficiales para tequileros">
                        @foreach ([
                            ['file' => 'aguacate-teq-transparent.png', 'name' => 'Aguacate'],
                            ['file' => 'nopal-teq-transparent.png', 'name' => 'Nopal'],
                            ['file' => 'chile-teq-transparent.png', 'name' => 'Chile'],
                            ['file' => 'pastor-teq-transparent.png', 'name' => 'Pastor'],
                            ['file' => 'elote-teq-transparent.png', 'name' => 'Elote'],
                            ['file' => 'botella-teq-transparent.png', 'name' => 'Botella'],
                        ] as $index => $design)
                            <figure class="{{ $index === 0 ? 'active' : '' }}" data-tequila-design="{{ asset('images/catalog/'.$design['file']) }}" data-tequila-name="{{ $design['name'] }}">
                                <img src="{{ asset('images/catalog/'.$design['file']) }}" alt="Diseno de {{ $design['name'] }} para tequilero">
                                <figcaption>{{ $design['name'] }}</figcaption>
                            </figure>
                        @endforeach
                    </div>

                    <p class="tequila-selection" data-tequila-selection>Vista: Blanco con diseno Aguacate</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-dark" href="https://wa.me/525564442949?text=Hola%2C%20quiero%20cotizar%20tequileros%20personalizados%20de%203%20o%206%20piezas" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar tequileros
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
