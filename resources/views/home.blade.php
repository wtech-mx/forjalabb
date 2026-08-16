@extends('layouts.app')

@section('title', 'ForjaLab | Taller de productos fisicos conectados')
@section('meta_description', 'Productos personalizados en CDMX: Biker Tags y Dog Tags con QR, corte laser, impresion 3D, sublimacion, DTF y soluciones conectadas.')
@section('seo_image', asset('images/forjalab-hero.png'))

@section('content')
    <div class="home-motion">
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="glass-copy">
                    <div class="eyebrow mb-3"><i class="bi bi-stars me-2"></i>Personalizamos · creamos · conectamos</div>
                    <h1 class="display-4 fw-bold mb-4">Productos personalizados que conectan lo fisico con lo digital.</h1>
                    <p class="lead text-secondary mb-4">ForjaLab convierte ideas en productos reales: placas QR, Biker Tags, tarjetas NFC, piezas 3D, sublimacion, DTF y soluciones web desde una pieza hasta volumen.</p>
                    <div class="hero-actions d-flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-dark btn-lg" href="#catalogo"><i class="bi bi-bag-heart-fill me-2"></i>Ver catalogo</a>
                        <a class="btn btn-outline-dark btn-lg" href="#servicios"><i class="bi bi-grid-1x2-fill me-2"></i>Servicios</a>
                    </div>
                    <div class="hero-metrics">
                        <div><i class="bi bi-box-seam-fill"></i><span><strong>1+</strong><small>pieza inicial</small></span></div>
                        <div><i class="bi bi-qr-code-scan"></i><span><strong>QR/NFC</strong><small>perfiles y paneles</small></span></div>
                        <div><i class="bi bi-geo-alt-fill"></i><span><strong>CDMX</strong><small>producción local</small></span></div>
                    </div>
                    </div>
                </div>
                <div class="col-lg-6 m-0">
                    <img class="hero-image" src="{{ asset('images/forjalab-hero.png') }}" alt="Productos personalizados con QR, NFC, sublimacion, DTF e impresion 3D" fetchpriority="high" decoding="async">
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad catalog-section catalog-section-priority" id="catalogo">
        <div class="container">
            <div class="home-section-heading">
                <div><span class="home-heading-icon"><i class="bi bi-bag-heart-fill"></i></span><div><div class="eyebrow">Catálogo ForjaLab</div><h2>Encuentra tu próxima idea.</h2><p>Productos listos para personalizar desde una pieza.</p></div></div>
                <a href="#servicios">Explorar servicios <i class="bi bi-arrow-down-right"></i></a>
            </div>

            @if ($catalogBundles->isNotEmpty())
                <div class="bundle-showcase">
                    <div class="bundle-showcase-heading">
                        <div>
                            <div class="eyebrow">Paquetes armados</div>
                        </div>
                    </div>
                    <div class="bundle-showcase-grid">
                        @foreach ($catalogBundles as $bundle)
                            <article class="bundle-home-card {{ $bundle->is_featured ? 'featured' : '' }}">
                                <div class="bundle-home-media">
                                    @if ($bundle->image_url)
                                        <img src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}" loading="lazy" decoding="async">
                                    @else
                                        <i class="bi bi-box-seam-fill"></i>
                                    @endif
                                </div>
                                <div class="bundle-home-body">
                                    <span class="badge text-bg-warning">{{ $bundle->is_featured ? 'Destacado' : 'Paquete' }}</span>
                                    <h4>{{ $bundle->name }}</h4>
                                    @if ($bundle->description)
                                        <p>{{ $bundle->description }}</p>
                                    @endif
                                    <div class="bundle-home-items">
                                        @foreach ($bundle->items->take(4) as $item)
                                            <span>{{ $item->quantity }}x {{ $item->product?->name }}</span>
                                        @endforeach
                                    </div>
                                    <div class="bundle-home-bottom">
                                        @if ($bundle->public_price > 0)
                                            <strong>${{ number_format((float) $bundle->public_price, 0) }}</strong>
                                        @endif
                                        <a class="btn btn-dark" href="{{ route('catalog.bundle.show', $bundle) }}">
                                            <i class="bi bi-arrow-right me-2"></i>Ver paquete
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="catalog-grid">
                @forelse ($catalogProducts as $product)
                    @include('catalog.partials.product-card', ['product' => $product])
                @empty
                    <div class="catalog-card">
                        <div class="catalog-body">
                            <h3>Catalogo en preparacion</h3>
                            <p>Pronto agregaremos productos disponibles para cotizar.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-pad" id="valor">
        <div class="container">
            <div class="row g-4 align-items-end mb-4">
                <div class="col-lg-7">
                    <div class="eyebrow"><i class="bi bi-gem me-2"></i>Propuesta de valor</div>
                    <h2 class="fw-bold mt-2">No vendemos solo objetos: forjamos producto mas tecnologia.</h2>
                </div>
            </div>
            <div class="row g-3 value-grid">
                @foreach ([
                    ['icon' => 'boxes', 'title' => 'Produccion hibrida', 'text' => 'Laser, 3D, sublimacion, DTF, QR, NFC y desarrollo web en un mismo flujo.'],
                    ['icon' => 'person-check', 'title' => 'Sin minimos elevados', 'text' => 'Atencion desde una pieza hasta campañas empresariales completas.'],
                    ['icon' => 'arrow-repeat', 'title' => 'Ingresos recurrentes', 'text' => 'Hosting, renovaciones, menus, soporte, dominios y reposiciones.'],
                    ['icon' => 'cpu', 'title' => 'Automatizacion interna', 'text' => 'Cotizacion, QR, archivos por lote, listas de nombres y seguimiento.'],
                ] as $item)
                    <div class="col-md-6 col-xl-3 value-grid-item">
                        <article class="feature-card h-100">
                            <i class="bi bi-{{ $item['icon'] }}"></i>
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['text'] }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad surface-band" id="servicios">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow"><i class="bi bi-magic me-2"></i>Personalización</div>
                    <h2 class="fw-bold mt-2 mb-0">Productos independientes</h2>
                </div>
                <p class="text-secondary mb-0 max-copy">Seguridad para bikers, placas inteligentes para mascotas y personalizacion textil con sublimacion y DTF.</p>
            </div>
            <div class="row g-4 independent-products-grid">
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-dark product-card-biker" href="{{ route('services.show', 'biker-tag') }}">
                        <i class="bi bi-shield-fill-plus service-card-icon"></i>
                        <span class="badge text-bg-warning">Emergencia</span>
                        <h3>Biker Tag QR</h3>
                        <p>Dog tag para motociclistas con perfil medico, contactos y opcion para motoclubes.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-light product-card-dog" href="{{ route('services.show', 'dog-tags') }}">
                        <i class="bi bi-heart-pulse-fill service-card-icon"></i>
                        <span class="badge text-bg-success">Mascotas</span>
                        <h3>Dog Tags QR</h3>
                        <p>Placas para mascota con perfil editable, WhatsApp y control de privacidad.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-light product-card-embroidery" href="{{ route('services.sublimation') }}">
                        <i class="bi bi-palette-fill service-card-icon"></i>
                        <span class="badge text-bg-light">Interactivo</span>
                        <h3>Sublimacion & DTF</h3>
                        <p>Sube tu logo, elige gorra, chamarra o playera y acomoda el diseño antes de cotizar.</p>
                        <span class="card-action">Abrir simulador <i class="bi bi-arrow-down-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-light product-card-laser" href="{{ route('services.laser') }}">
                        <i class="bi bi-lightning-charge-fill service-card-icon"></i>
                        <span class="badge text-bg-danger">Laser</span>
                        <h3>Laser personalizado</h3>
                        <p>Logo o escritura en termos, carcasas, parches para playera, madera y acrilico.</p>
                        <span class="card-action">Ver configurador <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="row g-4 audience-grid">
                @foreach ([
                    ['shop-window', 'Restaurantes', 'Bases QR, menú editable, WhatsApp, estadísticas y panel por sucursal.'],
                    ['buildings-fill', 'Corporativo', 'Kits, credenciales NFC, uniformes personalizados, portal interno y reposiciones por sede.'],
                    ['calendar2-heart-fill', 'Eventos', 'Invitaciones híbridas, RSVP, mapas, seating charts y recuerdos personalizados.'],
                    ['gear-wide-connected', 'Maquila', 'Producción sin marca para agencias, imprentas, wedding planners y diseñadores.'],
                ] as [$icon, $title, $text])
                    <div class="col-md-6 audience-grid-item">
                        <div class="line-card">
                            <span class="audience-icon"><i class="bi bi-{{ $icon }}"></i></span><div><h3>{{ $title }}</h3>
                            <p>{{ $text }}</p>
                            <small>Conocer soluciones <i class="bi bi-arrow-right"></i></small></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad instagram-section" id="redes">
        <div class="container">
            <div class="instagram-heading">
                <div>
                    <div class="eyebrow"><i class="bi bi-facebook me-2"></i>ForjaLab en comunidad</div>
                    <h2>Lo más reciente del taller.</h2>
                    <p>Novedades, procesos y productos publicados directamente desde Facebook.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap"><a class="btn btn-dark" href="https://www.facebook.com/people/ForjaLab/61593399118406/" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook me-2"></i>Seguir en Facebook</a><a class="btn btn-outline-dark" href="https://www.instagram.com/forjalabby/" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram me-2"></i>Instagram</a></div>
            </div>
            <div class="facebook-feed-shell">
                <iframe title="Publicaciones recientes de ForjaLab en Facebook" src="https://www.facebook.com/plugins/page.php?href={{ rawurlencode('https://www.facebook.com/61593399118406') }}&amp;tabs=timeline&amp;width=500&amp;height=680&amp;small_header=false&amp;adapt_container_width=true&amp;hide_cover=false&amp;show_facepile=true" width="500" height="680" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" loading="lazy"></iframe>
                <div class="facebook-feed-copy"><span class="social-proof-icon"><i class="bi bi-facebook"></i></span><h3>Ideas recién salidas del taller</h3><p>Este muro se actualiza automáticamente cuando publicamos algo nuevo. No necesitas salir de ForjaLab para descubrirlo.</p><ul><li><i class="bi bi-check-circle-fill"></i> Publicaciones recientes</li><li><i class="bi bi-check-circle-fill"></i> Fotos y novedades</li><li><i class="bi bi-check-circle-fill"></i> Sin registros ni aplicaciones</li></ul><a href="https://www.facebook.com/people/ForjaLab/61593399118406/" target="_blank" rel="noopener noreferrer">Abrir página completa <i class="bi bi-arrow-up-right"></i></a></div>
            </div>
        </div>
    </section>

    <section class="section-pad launch-band" id="lanzamiento">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <div class="eyebrow"><i class="bi bi-chat-dots-fill me-2"></i>Cotiza sin vueltas</div>
                    <h2 class="fw-bold mt-2">Dinos que quieres fabricar y armamos una propuesta clara.</h2>
                    <div class="launch-actions">
                        <a class="btn btn-light" href="#catalogo"><i class="bi bi-bag-heart me-2"></i>Ver catalogo</a>
                        <a class="btn btn-outline-light" href="https://wa.me/525564442949?text={{ rawurlencode('Hola, quiero cotizar un producto personalizado con ForjaLab.') }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar</a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="timeline">
                        <div><i class="bi bi-bag-check-fill"></i><strong>Producto</strong><span>Elige una pieza, paquete o idea especial.</span></div>
                        <div><i class="bi bi-123"></i><strong>Cantidad</strong><span>Define si será individual, set o pedido por volumen.</span></div>
                        <div><i class="bi bi-vector-pen"></i><strong>Diseño</strong><span>Mándanos logo, texto, foto o referencia visual.</span></div>
                        <div><i class="bi bi-truck"></i><strong>Entrega</strong><span>Confirmamos costo, tiempo y detalles de producción.</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>

    <script>
        const homeRevealTargets = document.querySelectorAll([
            '.home-motion section:not(.hero-section) h2',
            '.home-motion .bundle-home-card',
            '.home-motion .catalog-card',
            '.home-motion .value-grid-item',
            '.home-motion .independent-products-item',
            '.home-motion .embroidery-steps > div',
            '.home-motion .embroidery-tool',
            '.home-motion .audience-grid-item',
            '.home-motion .timeline > div',
            '.home-motion .facebook-feed-shell'
        ].join(','));

        homeRevealTargets.forEach((element, index) => {
            element.dataset.reveal = '';
            element.style.setProperty('--reveal-delay', `${(index % 4) * 80}ms`);
        });

        if ('IntersectionObserver' in window) {
            const homeObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -35px' });

            homeRevealTargets.forEach((element) => homeObserver.observe(element));
        } else {
            homeRevealTargets.forEach((element) => element.classList.add('is-visible'));
        }
    </script>
@endsection
