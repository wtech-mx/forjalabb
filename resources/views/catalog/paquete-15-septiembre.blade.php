@extends('layouts.app')

@section('title', 'Paquete 15 de septiembre | ForjaLab')

@section('content')
    <section class="section-pad package-page">
        <div class="container">
            <div class="row g-4 align-items-center mb-4">
                <div class="col-lg-6">
                    <div class="eyebrow">Paquete de temporada</div>
                    <h1 class="fw-bold mt-2">Tabla + 2 tequileros + botella</h1>
                    <p class="lead text-secondary">Un set listo para celebrar el 15 de septiembre: tabla grabada, dos tequileros personalizados y botella licorera para armar una presentacion completa.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-dark" href="https://wa.me/?text=Hola%2C%20quiero%20cotizar%20el%20paquete%2015%20de%20septiembre%20con%20tabla%2C%202%20tequileros%20y%20botella" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar paquete
                        </a>
                        <a class="btn btn-outline-dark" href="{{ route('home') }}#catalogo">
                            <i class="bi bi-arrow-left me-2"></i>Volver al catalogo
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="package-summary">
                        <div><i class="bi bi-grid-3x3-gap"></i><span>Tabla grabada</span></div>
                        <div><i class="bi bi-cup-straw"></i><span>2 tequileros</span></div>
                        <div><i class="bi bi-bottle"></i><span>Botella licorera</span></div>
                    </div>
                </div>
            </div>

            <div id="packageCarousel" class="carousel slide package-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach ([0, 1, 2, 3] as $index)
                        <button type="button" data-bs-target="#packageCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Foto {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach ([
                        ['file' => 'paquete-15-septiembre.png', 'title' => 'Paquete completo', 'alt' => 'Paquete completo de tabla, dos tequileros y botella para 15 de septiembre'],
                        ['file' => 'paquete-15-septiembre-tequileros.png', 'title' => 'Tequileros personalizados', 'alt' => 'Acercamiento de dos tequileros personalizados del paquete'],
                        ['file' => 'paquete-15-septiembre-tabla.png', 'title' => 'Tabla grabada', 'alt' => 'Detalle de tabla grabada Viva Mexico con limones y sal'],
                        ['file' => 'paquete-15-septiembre-botella.png', 'title' => 'Botella licorera', 'alt' => 'Detalle de botella licorera del paquete de temporada'],
                    ] as $index => $slide)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="package-slide-frame">
                                <img src="{{ asset('images/catalog/'.$slide['file']) }}" alt="{{ $slide['alt'] }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async">
                            </div>
                            <div class="carousel-caption">
                                <span>{{ $slide['title'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#packageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#packageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>
@endsection
