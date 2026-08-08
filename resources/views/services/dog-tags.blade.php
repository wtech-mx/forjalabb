@extends('layouts.app')

@section('title', 'Dog Tag QR para mascotas | ForjaLab')
@section('meta_description', 'Placa QR personalizada para perros con datos de contacto, veterinaria y cuidados. Perfil activo de por vida y sin mensualidades.')
@section('seo_image', asset('images/dog-tag-walk.png'))
@section('seo_type', 'product')
@section('structured_data')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org', '@type' => 'Product', 'name' => 'Dog Tag QR para mascotas',
        'description' => 'Placa QR personalizada para perros con perfil de contacto, cuidados y veterinaria, activa de por vida y sin mensualidades.',
        'image' => [asset('images/dog-tag-walk.png'), asset('images/dog-tag-options.png')], 'brand' => ['@type' => 'Brand', 'name' => 'ForjaLab'],
        'offers' => ['@type' => 'AggregateOffer', 'priceCurrency' => 'MXN', 'lowPrice' => '150', 'highPrice' => '180', 'offerCount' => '2', 'availability' => 'https://schema.org/InStock', 'url' => route('services.show', 'dog-tags')],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
    @php($whatsapp = 'https://wa.me/525564442949?text='.urlencode('Hola, quiero cotizar un Dog Tag QR para mi mascota'))

    <main class="dog-sales-page">
        <section class="dog-sales-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="dog-overline"><i class="bi bi-heart-fill"></i> Identificacion inteligente para mascotas</span>
                        <h1>Si se pierde, su placa ayuda a volver a casa.</h1>
                        <p>Una placa personalizada con QR que permite consultar sus datos y contactar a su familia desde cualquier celular, sin instalar aplicaciones.</p>
                        <div class="dog-hero-actions">
                            <a class="btn btn-success btn-lg" href="{{ $whatsapp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar su Dog Tag</a>
                            <a href="#como-funciona">Ver como funciona <i class="bi bi-arrow-down"></i></a>
                        </div>
                        <div class="dog-trust">
                            <span><i class="bi bi-infinity"></i><b>Sin mensualidades</b></span>
                            <span><i class="bi bi-phone"></i><b>Sin aplicaciones</b></span>
                            <span><i class="bi bi-palette"></i><b>Personalizada</b></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="dog-hero-photo">
                            <img src="{{ asset('images/dog-tag-walk.png') }}" alt="Perro paseando con una placa Dog Tag QR" loading="eager" fetchpriority="high" decoding="async">
                            <div class="dog-price-badge"><small>Desde</small><strong>$150 MXN</strong><span>pago unico</span></div>
                            <div class="dog-home-badge"><i class="bi bi-house-heart-fill"></i><span>Su camino<br><b>de regreso</b></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dog-benefit-strip">
            <div class="container"><div class="row g-0">
                <div class="col-6 col-lg-3"><div><i class="bi bi-person-lines-fill"></i><span>Contacto del<br><b>responsable</b></span></div></div>
                <div class="col-6 col-lg-3"><div><i class="bi bi-geo-alt-fill"></i><span>Ubicacion del<br><b>escaneo</b></span></div></div>
                <div class="col-6 col-lg-3"><div><i class="bi bi-hospital-fill"></i><span>Veterinaria y<br><b>cuidados</b></span></div></div>
                <div class="col-6 col-lg-3"><div><i class="bi bi-whatsapp"></i><span>Contacto por<br><b>WhatsApp</b></span></div></div>
            </div></div>
        </section>

        <section class="dog-product section-pad">
            <div class="container"><div class="row align-items-center g-5">
                <div class="col-lg-6"><img src="{{ asset('images/dog-tag-options.png') }}" alt="Opciones de placas QR personalizadas para perros" loading="lazy" decoding="async"></div>
                <div class="col-lg-6">
                    <span class="dog-overline dark">Una placa tan unica como tu mascota</span>
                    <h2>Personalizada, bonita y realmente util.</h2>
                    <p>Elige la forma, nombre y estilo. Cada placa lleva un QR individual conectado al perfil de tu mascota.</p>
                    <div class="dog-product-points">
                        <span><i class="bi bi-stars"></i><b>Diseño personalizado</b><small>Nombre, color y estilo.</small></span>
                        <span><i class="bi bi-qr-code-scan"></i><b>QR individual</b><small>Un perfil exclusivo.</small></span>
                        <span><i class="bi bi-shield-check"></i><b>Perfil de por vida</b><small>Sin pagos mensuales.</small></span>
                    </div>
                </div>
            </div></div>
        </section>

        <section class="dog-visual-break">
            <img src="{{ asset('images/dog-tags-hero.png') }}" alt="Perro con placa personalizada y perfil digital" loading="lazy" decoding="async">
            <div class="container"><div><span><i class="bi bi-paw-fill"></i> Siempre identificado</span><h2>Una pequeña placa.<br>Una gran tranquilidad.</h2></div></div>
        </section>

        <section class="dog-how section-pad" id="como-funciona">
            <div class="container"><div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1">
                    <span class="dog-overline dark">Asi de sencillo</span>
                    <h2>Escanear. Contactar. Volver a casa.</h2>
                    <p>Si alguien encuentra a tu mascota, puede ayudarte en segundos.</p>
                    <ol class="dog-steps">
                        <li><span>01</span><div><b>Escanea la placa</b><small>La camara abre el perfil QR.</small></div></li>
                        <li><span>02</span><div><b>Consulta sus datos</b><small>Nombre, cuidados y responsables.</small></div></li>
                        <li><span>03</span><div><b>Contacta a su familia</b><small>Llamada o WhatsApp en un toque.</small></div></li>
                        <li><span>04</span><div><b>Comparte la ubicacion</b><small>Ayuda a indicar donde fue encontrado.</small></div></li>
                    </ol>
                </div>
                <div class="col-lg-6 order-1 order-lg-2"><img src="{{ asset('images/dog-tag-scan.png') }}" alt="Persona escaneando el Dog Tag QR de un perro" loading="lazy" decoding="async"></div>
            </div></div>
        </section>

        <section class="dog-profile section-pad">
            <div class="container">
                <div class="dog-section-heading text-center"><span class="dog-overline">Todo lo importante</span><h2>Su perfil ayuda a cuidarlo y localizarte</h2><p>Tu decides que informacion compartir y puedes solicitar cambios cuando lo necesites.</p></div>
                <div class="row g-3 dog-data-grid">
                    @foreach ([
                        ['bi-person-badge-fill', 'Nombre y fotografia', 'Identificacion clara de tu mascota.'],
                        ['bi-telephone-fill', 'Responsable', 'Telefono, WhatsApp y contacto secundario.'],
                        ['bi-balloon-heart-fill', 'Raza y especie', 'Datos que ayudan a reconocerlo.'],
                        ['bi-heart-pulse-fill', 'Cuidados y alergias', 'Indicaciones importantes para protegerlo.'],
                        ['bi-hospital-fill', 'Veterinaria', 'Nombre, telefono y correo de su veterinaria.'],
                        ['bi-geo-alt-fill', 'Ubicacion', 'Alerta opcional con el lugar del escaneo.'],
                    ] as [$icon, $title, $copy])
                        <div class="col-md-6 col-lg-4"><article><i class="bi {{ $icon }}"></i><h3>{{ $title }}</h3><p>{{ $copy }}</p></article></div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dog-lost section-pad">
            <div class="container"><div class="dog-lost-panel">
                <div class="dog-lost-icon"><i class="bi bi-house-heart-fill"></i></div>
                <div><span class="dog-overline dark">Pensado para ese momento</span><h2>Quien lo encuentre sabra a quien llamar.</h2><p>La placa no depende de una bateria ni exige crear una cuenta. El perfil se abre directamente y muestra las formas de contacto que hayas autorizado.</p></div>
                <div class="dog-contact-preview"><span><i class="bi bi-telephone-fill"></i> Llamar</span><span><i class="bi bi-whatsapp"></i> WhatsApp</span><span><i class="bi bi-geo-alt-fill"></i> Ubicacion</span></div>
            </div></div>
        </section>

        <section class="dog-offer section-pad"><div class="container"><div class="dog-offer-card">
            <div><span class="dog-overline">Proteccion sin mensualidades</span><h2>Dog Tag QR desde <strong>$150 MXN</strong></h2><p>El precio final depende de la forma, material y diseño elegido. Confirmamos todo contigo antes de producir.</p></div>
            <div><span><i class="bi bi-check2"></i> Placa personalizada</span><span><i class="bi bi-check2"></i> Perfil QR incluido</span><span><i class="bi bi-check2"></i> Activacion de por vida</span><span><i class="bi bi-arrow-repeat"></i> Cambios posteriores: $50</span></div>
            <a class="btn btn-success btn-lg" href="{{ $whatsapp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar su placa</a>
        </div></div></section>

        <a class="dog-mobile-cta" href="{{ $whatsapp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> Cotizar Dog Tag</a>
    </main>

    <script>
        const dogRevealTargets = document.querySelectorAll('.dog-sales-page section h2, .dog-product img, .dog-product-points span, .dog-steps li, .dog-how img, .dog-data-grid article, .dog-lost-panel');
        dogRevealTargets.forEach((element, index) => {
            element.dataset.reveal = '';
            element.style.setProperty('--reveal-delay', `${(index % 6) * 65}ms`);
        });
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, currentObserver) => entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            }), { threshold: 0.12 });
            dogRevealTargets.forEach((element) => observer.observe(element));
        } else {
            dogRevealTargets.forEach((element) => element.classList.add('is-visible'));
        }
    </script>
@endsection
