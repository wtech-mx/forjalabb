@extends('layouts.app')

@section('title', $service['title'].' | ForjaLab')

@section('content')
    <section class="service-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="glass-copy">
                    <div class="eyebrow">{{ $service['eyebrow'] }}</div>
                    <h1 class="display-5 fw-bold mt-3 mb-3">{{ $service['title'] }}</h1>
                    <p class="lead text-secondary mb-4">{{ $service['short'] }}</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-dark btn-lg" href="https://wa.me/?text=Hola%2C%20quiero%20cotizar%20{{ urlencode($service['title']) }}" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Cotizar por WhatsApp
                        </a>
                        <a class="btn btn-outline-dark btn-lg" href="{{ route('home') }}#servicios">
                            <i class="bi bi-arrow-left me-2"></i>Mas servicios
                        </a>
                    </div>
                    <div class="price-strip">
                        <div>
                            <span>Precio estimado</span>
                            <strong>{{ $service['price'] }}</strong>
                        </div>
                        <div>
                            <span>Renovacion</span>
                            <strong>{{ $service['renewal'] }}</strong>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img class="hero-image" src="{{ asset('images/'.$service['hero_image']) }}" alt="{{ $service['title'] }}" fetchpriority="high" decoding="async">
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="sticky-copy">
                        <div class="eyebrow">Para quien es</div>
                        <h2 class="fw-bold mt-2">Una pieza fisica con respaldo digital.</h2>
                        <p class="text-secondary">{{ $service['audience'] }}</p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        @foreach ($service['features'] as $feature)
                            <div class="col-md-6">
                                <div class="check-item">
                                    <i class="bi bi-check2-circle"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad surface-band">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow">Paquetes sugeridos</div>
                    <h2 class="fw-bold mt-2 mb-0">Precios por definir con costos reales.</h2>
                </div>
                <p class="text-secondary max-copy mb-0">Los rangos sirven para construir la landing, validar demanda y ajustar margen cuando tengamos tiempos y merma medidos.</p>
            </div>
            <div class="row g-4">
                @foreach ($service['packages'] as $package)
                    <div class="col-md-4">
                        <article class="package-card h-100">
                            <h3>{{ $package['name'] }}</h3>
                            <div class="package-price">{{ $package['range'] }}</div>
                            <p>{{ $package['items'] }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="cta-panel">
                <div>
                    <div class="eyebrow">Siguiente paso</div>
                    <h2 class="fw-bold mt-2 mb-2">Crear muestra, medir costo y publicar oferta.</h2>
                    <p class="mb-0 text-secondary">La landing ya queda lista para conectar formulario, pagos, perfiles QR y administracion con MySQL.</p>
                </div>
                <a class="btn btn-dark btn-lg" href="https://wa.me/?text=Hola%2C%20quiero%20una%20muestra%20de%20{{ urlencode($service['title']) }}" target="_blank" rel="noopener">
                    <i class="bi bi-send me-2"></i>Pedir muestra
                </a>
            </div>
        </div>
    </section>
@endsection
