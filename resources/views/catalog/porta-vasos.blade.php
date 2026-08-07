@extends('layouts.app')

@section('title', 'Porta vasos 15 de septiembre | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow">Set de temporada</div>
                    <h1 class="fw-bold mt-2 mb-0">Porta vasos 15 de septiembre</h1>
                </div>
                <p class="text-secondary mb-0 max-copy">Elige un diseno para ver como luce en porta vasos de pizarra negra. El set incluye 4 piezas y puedes pedirlos iguales o combinados.</p>
            </div>

            <article class="tequila-feature coaster-feature" data-coaster-configurator>
                <div class="tequila-photo coaster-photo">
                    <div class="coaster-preview" data-coaster-preview>
                        <div class="coaster-tile">
                            <img src="{{ asset('images/catalog/porta-vasos-viva-mexico-square.png') }}" alt="Porta vasos negro con diseno Viva Mexico" data-coaster-preview-design>
                        </div>
                        <div class="tequila-shadow coaster-shadow" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="tequila-copy">
                    <span class="badge text-bg-success mb-3">Vista interactiva</span>
                    <h2>Arma tu set</h2>
                    <p>Selecciona el diseno que quieres mostrar. La vista es una referencia rapida para cotizar un set de 4 porta vasos para temporada patria.</p>

                    <div class="design-strip coaster-design-strip" aria-label="Disenos para porta vasos">
                        @foreach ([
                            ['file' => 'porta-vasos-viva-mexico-square.png', 'name' => 'Viva Mexico', 'alt' => 'Porta vasos Viva Mexico 15 de septiembre'],
                            ['file' => 'porta-vasos-aguila-nopal-square.png', 'name' => 'Aguila y nopal', 'alt' => 'Porta vasos con aguila mexicana y nopal'],
                            ['file' => 'porta-vasos-salud-agave-square.png', 'name' => 'Salud agave', 'alt' => 'Porta vasos con agave tequila y texto salud'],
                            ['file' => 'porta-vasos-fiesta-mexicana-square.png', 'name' => 'Fiesta mexicana', 'alt' => 'Porta vasos con flor y papel picado'],
                        ] as $index => $design)
                            <figure class="{{ $index === 0 ? 'active' : '' }}" data-coaster-design="{{ asset('images/catalog/'.$design['file']) }}" data-coaster-name="{{ $design['name'] }}" data-coaster-alt="{{ $design['alt'] }}">
                                <img src="{{ asset('images/catalog/'.$design['file']) }}" alt="{{ $design['alt'] }}">
                                <figcaption>{{ $design['name'] }}</figcaption>
                            </figure>
                        @endforeach
                    </div>

                    <p class="tequila-selection" data-coaster-selection>Vista: Porta vasos con diseno Viva Mexico</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-dark" data-coaster-quote href="https://wa.me/525564442949?text=Hola%2C%20quiero%20cotizar%20un%20set%20de%204%20porta%20vasos%20del%2015%20de%20septiembre.%20Me%20interesa%20el%20diseno%20Viva%20Mexico." target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar set
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
