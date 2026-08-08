@extends('layouts.app')

@section('title', 'Biker Tag QR de emergencia | ForjaLab')
@section('meta_description', 'Identificacion QR para motociclistas con contactos, datos medicos, seguro vehicular y servicio de salud. Sin mensualidades.')
@section('seo_image', asset('images/biker-tag-hero.png'))
@section('seo_type', 'product')
@section('structured_data')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org', '@type' => 'Product', 'name' => 'Kit Biker Tag QR de emergencia',
        'description' => 'Kit de dos placas militares QR para motociclista y llavero de la moto, conectado a un perfil de emergencia sin mensualidades.',
        'image' => [asset('images/biker-tag-hero.png'), asset('images/biker-tag-detail.png')], 'brand' => ['@type' => 'Brand', 'name' => 'ForjaLab'],
        'offers' => ['@type' => 'Offer', 'priceCurrency' => 'MXN', 'price' => '250', 'availability' => 'https://schema.org/InStock', 'url' => route('services.show', 'biker-tag')],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
    @php($whatsapp = 'https://wa.me/525564442949?text='.urlencode('Hola, quiero cotizar mi Biker Tag QR'))

    <main class="biker-sales-page">
        <section class="biker-sales-hero">
            <div class="container position-relative">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="biker-overline"><i class="bi bi-qr-code-scan"></i> Identificacion QR para motociclistas</span>
                        <h1>En el camino, tus datos importantes viajan contigo.</h1>
                        <p class="biker-hero-copy">Un tag personalizado que permite consultar contactos de emergencia, informacion medica y seguros con solo escanear su QR.</p>
                        <div class="biker-hero-actions">
                            <a class="btn btn-warning btn-lg" href="{{ $whatsapp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar mi Biker Tag</a>
                            <a class="biker-text-link" href="#como-funciona">Ver como funciona <i class="bi bi-arrow-down"></i></a>
                        </div>
                        <div class="biker-trust-row">
                            <span><i class="bi bi-infinity"></i><strong>Sin mensualidad</strong> perfil de por vida</span>
                            <span><i class="bi bi-sliders"></i><strong>Personalizable</strong> tu eliges los datos</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="biker-hero-media">
                            <img src="{{ asset('images/biker-tag-hero.png') }}" alt="Biker Tag personalizado con codigo QR y perfil digital de emergencia" loading="eager" fetchpriority="high" decoding="async">
                            <div class="biker-price-float"><small>Kit completo</small><strong>$250 MXN</strong><span>pago unico</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="biker-kit section-pad">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <span class="biker-overline dark"><i class="bi bi-shield-fill-check"></i> Doble identificacion</span>
                        <h2>Dos placas. Una misma red de proteccion.</h2>
                        <p>El kit incluye dos placas tipo militar vinculadas a tu perfil de emergencia. Asi tu informacion puede acompañar tanto a la motocicleta como a ti.</p>
                        <div class="biker-kit-price"><small>Kit Biker Tag</small><strong>$250 <em>MXN</em></strong><span>Pago unico · Perfil sin mensualidad</span></div>
                    </div>
                    <div class="col-lg-7">
                        <div class="biker-tag-pair">
                            <button class="dogtag-flip moto-tag" type="button" aria-label="Voltear placa de la motocicleta" aria-pressed="false" data-dogtag-flip>
                                <span class="dogtag-3d">
                                    <span class="biker-dogtag dogtag-front">
                                        <span class="dogtag-hole"></span><i class="bi bi-bicycle"></i><strong>MOTO</strong><span class="dogtag-qr"><i class="bi bi-qr-code"></i></span>
                                        <small>Escanea en emergencia</small>
                                    </span>
                                    <span class="biker-dogtag dogtag-back">
                                        <span class="dogtag-hole"></span><small class="dogtag-ice">ICE · DATOS DE EMERGENCIA</small>
                                        <span class="dogtag-info">
                                            <span><i class="bi bi-person-fill"></i><b>Nombre</b><em>Rider ForjaLab</em></span>
                                            <span><i class="bi bi-droplet-fill"></i><b>Sangre</b><em>A+</em></span>
                                            <span><i class="bi bi-bicycle"></i><b>Moto</b><em>MT-07 · ABC123</em></span>
                                            <span><i class="bi bi-shield-check"></i><b>Poliza</b><em>MX-000123</em></span>
                                            <span><i class="bi bi-hospital-fill"></i><b>Institucion</b><em>IMSS</em></span>
                                            <span><i class="bi bi-person-vcard-fill"></i><b>No. seguro</b><em>987654321</em></span>
                                            <span class="dogtag-contact"><i class="bi bi-telephone-fill"></i><b>ICE</b><em>55 0000 0000</em></span>
                                        </span>
                                    </span>
                                </span>
                                <span class="dogtag-caption"><i class="bi bi-arrow-repeat"></i> Para las llaves · Toca para voltear</span>
                            </button>
                            <div class="biker-pair-link"><i class="bi bi-link-45deg"></i><span>Mismo perfil<br>de emergencia</span></div>
                            <button class="dogtag-flip rider-tag" type="button" aria-label="Voltear placa del motociclista" aria-pressed="false" data-dogtag-flip>
                                <span class="dogtag-3d">
                                    <span class="biker-dogtag dogtag-front">
                                        <span class="dogtag-hole"></span><i class="bi bi-person-arms-up"></i><strong>RIDER</strong><span class="dogtag-qr"><i class="bi bi-qr-code"></i></span>
                                        <small>Escanea en emergencia</small>
                                    </span>
                                    <span class="biker-dogtag dogtag-back">
                                        <span class="dogtag-hole"></span><small class="dogtag-ice">ICE · DATOS DE EMERGENCIA</small>
                                        <span class="dogtag-info">
                                            <span><i class="bi bi-person-fill"></i><b>Nombre</b><em>Rider ForjaLab</em></span>
                                            <span><i class="bi bi-droplet-fill"></i><b>Sangre</b><em>A+</em></span>
                                            <span><i class="bi bi-bicycle"></i><b>Moto</b><em>MT-07 · ABC123</em></span>
                                            <span><i class="bi bi-shield-check"></i><b>Poliza</b><em>MX-000123</em></span>
                                            <span><i class="bi bi-hospital-fill"></i><b>Institucion</b><em>IMSS</em></span>
                                            <span><i class="bi bi-person-vcard-fill"></i><b>No. seguro</b><em>987654321</em></span>
                                            <span class="dogtag-contact"><i class="bi bi-telephone-fill"></i><b>ICE</b><em>55 0000 0000</em></span>
                                        </span>
                                    </span>
                                </span>
                                <span class="dogtag-caption"><i class="bi bi-arrow-repeat"></i> Para el rider · Toca para voltear</span>
                            </button>
                        </div>
                        <p class="dogtag-demo-note"><i class="bi bi-info-circle-fill"></i> Ambas placas llevan la misma informacion completa. Los datos grabados se personalizan para cada usuario.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="biker-quick-benefits" aria-label="Beneficios principales">
            <div class="container">
                <div class="row g-0">
                    <div class="col-6 col-lg-3"><div><i class="bi bi-person-lines-fill"></i><span>Contactos de<br><strong>emergencia</strong></span></div></div>
                    <div class="col-6 col-lg-3"><div><i class="bi bi-heart-pulse"></i><span>Datos medicos<br><strong>importantes</strong></span></div></div>
                    <div class="col-6 col-lg-3"><div><i class="bi bi-shield-check"></i><span>Poliza y servicio<br><strong>de salud</strong></span></div></div>
                    <div class="col-6 col-lg-3"><div><i class="bi bi-geo-alt"></i><span>Alerta con<br><strong>ubicacion GPS</strong></span></div></div>
                </div>
            </div>
        </section>

        <section class="biker-ride-break" aria-label="Proteccion durante cada rodada">
            <img src="{{ asset('images/biker-tag-ride.png') }}" alt="Motociclistas equipados rodando por una carretera de montana" loading="lazy" decoding="async">
            <div class="container">
                <div class="biker-ride-message">
                    <span><i class="bi bi-shield-fill-check"></i> Prevencion en ruta</span>
                    <h2>Disfruta la rodada.<br>Tu informacion va contigo.</h2>
                    <a href="#como-funciona">Conoce la proteccion <i class="bi bi-arrow-down-right"></i></a>
                </div>
            </div>
        </section>

        <section class="biker-story section-pad" id="como-funciona">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6"><img src="{{ asset('images/biker-tag-scan.png') }}" alt="Motociclista consultando un Biker Tag QR con su celular" loading="lazy" decoding="async"></div>
                    <div class="col-lg-6">
                        <span class="biker-overline dark">Simple y rapido</span>
                        <h2>Escanear. Consultar. Actuar.</h2>
                        <p>Quien encuentre el tag puede abrir el perfil desde la camara de cualquier celular, sin instalar aplicaciones.</p>
                        <ol class="biker-steps">
                            <li><span>01</span><div><strong>Escanean el QR</strong><small>El perfil abre directamente en el navegador.</small></div></li>
                            <li><span>02</span><div><strong>Ven la informacion autorizada</strong><small>Datos medicos, seguros y contactos que tu decidas compartir.</small></div></li>
                            <li><span>03</span><div><strong>Contactan a tu red</strong><small>Pueden llamar, enviar WhatsApp y compartir la ubicacion.</small></div></li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="biker-profile-section section-pad">
            <div class="container">
                <div class="text-center biker-section-heading">
                    <span class="biker-overline">Tu perfil, tus datos</span>
                    <h2>Informacion util cuando cada minuto cuenta</h2>
                    <p>Capturamos solo lo que quieras hacer visible. Puedes solicitar cambios cuando tu informacion se actualice.</p>
                </div>
                <div class="row g-3 biker-data-grid">
                    @foreach ([
                        ['bi-droplet-half', 'Datos medicos', 'Tipo de sangre, alergias, donador y notas medicas.'],
                        ['bi-telephone-outbound', 'Contactos directos', 'Telefono y WhatsApp de tus contactos de emergencia.'],
                        ['bi-bicycle', 'Datos de la moto', 'Vehiculo, placas y club para una identificacion clara.'],
                        ['bi-shield-fill-check', 'Seguro vehicular', 'Numero de poliza y fecha de fin de vigencia.'],
                        ['bi-hospital', 'Servicio de salud', 'IMSS, ISSSTE, PEMEX, SEDENA, SEMAR u otra institucion.'],
                        ['bi-crosshair', 'Ubicacion del escaneo', 'Opcion para enviar coordenadas a tus contactos autorizados.'],
                    ] as [$icon, $title, $copy])
                        <div class="col-md-6 col-lg-4"><article><i class="bi {{ $icon }}"></i><h3>{{ $title }}</h3><p>{{ $copy }}</p></article></div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="biker-emergency-band section-pad">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <figure class="biker-responder-photo">
                            <img src="{{ asset('images/biker-tag-responder.png') }}" alt="Respondiente consultando la informacion medica de un Biker Tag" loading="lazy" decoding="async">
                            <figcaption><i class="bi bi-qr-code-scan"></i> Acceso inmediato desde cualquier celular</figcaption>
                        </figure>
                        <div class="emergency-symbol"><i class="bi bi-star-of-life"></i><span>ICE</span></div>
                        <span class="biker-overline">Informacion para emergencias</span>
                        <h2>Tu Biker Tag habla por ti cuando tu no puedes.</h2>
                        <p>El perfil presenta primero la informacion que puede ayudar a un testigo o personal de emergencia a tomar decisiones y localizar a tu familia.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="emergency-action-grid">
                            <article><i class="bi bi-heart-pulse-fill"></i><div><strong>Identificacion medica</strong><span>Sangre, alergias y notas importantes</span></div></article>
                            <article><i class="bi bi-telephone-fill"></i><div><strong>Contacto ICE</strong><span>Llamada y WhatsApp en un toque</span></div></article>
                            <article><i class="bi bi-hospital-fill"></i><div><strong>Institucion de salud</strong><span>Numero de afiliacion y cobertura</span></div></article>
                            <article><i class="bi bi-shield-fill-exclamation"></i><div><strong>Seguro de la moto</strong><span>Poliza y fecha de vigencia</span></div></article>
                            <article><i class="bi bi-geo-alt-fill"></i><div><strong>Ubicacion GPS</strong><span>Coordenadas para la red de emergencia</span></div></article>
                            <article><i class="bi bi-phone-fill"></i><div><strong>Sin aplicaciones</strong><span>Se abre desde cualquier navegador</span></div></article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="biker-craft section-pad">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5 order-2 order-lg-1">
                        <span class="biker-overline dark">Hecho para tu estilo</span>
                        <h2>Una pieza personal, no una etiqueta generica.</h2>
                        <p>Elige nombre, estilo y acabado. Antes de fabricar te confirmamos por WhatsApp el diseño y precio final.</p>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Diseño personalizado</li>
                            <li><i class="bi bi-check-circle-fill"></i> QR individual vinculado a tu perfil</li>
                            <li><i class="bi bi-check-circle-fill"></i> Precio final confirmado antes de producir</li>
                        </ul>
                    </div>
                    <div class="col-lg-7 order-1 order-lg-2"><img src="{{ asset('images/biker-tag-detail.png') }}" alt="Detalle de placa metalica Biker Tag con codigo QR" loading="lazy" decoding="async"></div>
                </div>
            </div>
        </section>

        <section class="biker-price-section section-pad">
            <div class="container">
                <div class="biker-offer-card">
                    <div><span class="biker-overline">Proteccion doble</span><h2>Kit de dos placas por <strong>$250 MXN</strong></h2><p>Una placa militar para las llaves de tu moto y otra para llevar contigo. Ambas conectadas a tu perfil digital, sin mensualidades.</p></div>
                    <div class="biker-offer-points"><span><i class="bi bi-check2"></i> 2 placas tipo militar</span><span><i class="bi bi-check2"></i> Perfil QR de emergencia</span><span><i class="bi bi-check2"></i> Activacion incluida</span><span><i class="bi bi-arrow-repeat"></i> Cambios posteriores: $50</span></div>
                    <a class="btn btn-warning btn-lg" href="{{ $whatsapp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Quiero cotizar el mio</a>
                </div>
            </div>
        </section>

        <a class="biker-mobile-cta" href="{{ $whatsapp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> Cotizar Biker Tag</a>
    </main>

    <script>
        document.querySelectorAll('[data-dogtag-flip]').forEach((tag) => {
            tag.addEventListener('click', () => {
                const flipped = tag.classList.toggle('is-flipped');
                tag.setAttribute('aria-pressed', flipped ? 'true' : 'false');
            });
        });

        const revealTargets = document.querySelectorAll('.biker-sales-page section h2, .biker-data-grid article, .biker-steps li, .emergency-action-grid article, .biker-story img, .biker-craft img, .biker-responder-photo');
        revealTargets.forEach((element, index) => {
            element.dataset.reveal = '';
            element.style.setProperty('--reveal-delay', `${(index % 6) * 70}ms`);
        });

        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, { threshold: 0.12 });

            revealTargets.forEach((element) => revealObserver.observe(element));
        } else {
            revealTargets.forEach((element) => element.classList.add('is-visible'));
        }
    </script>
@endsection
