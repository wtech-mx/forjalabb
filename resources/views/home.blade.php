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
                    <div class="eyebrow mb-3">Personalizamos · creamos · conectamos</div>
                    <h1 class="display-4 fw-bold mb-4">Productos personalizados que conectan lo fisico con lo digital.</h1>
                    <p class="lead text-secondary mb-4">ForjaLab convierte ideas en productos reales: placas QR, Biker Tags, tarjetas NFC, piezas 3D, sublimacion, DTF y soluciones web desde una pieza hasta volumen.</p>
                    <div class="hero-actions d-flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-dark btn-lg" href="#catalogo"><i class="bi bi-bag-heart-fill me-2"></i>Ver catalogo</a>
                        <a class="btn btn-outline-dark btn-lg" href="#servicios"><i class="bi bi-grid-1x2-fill me-2"></i>Servicios</a>
                    </div>
                    <div class="hero-metrics">
                        <div><strong>1+</strong><span>pieza inicial</span></div>
                        <div><strong>QR/NFC</strong><span>perfiles y paneles</span></div>
                        <div><strong>CDMX</strong><span>produccion local</span></div>
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
                    <div class="eyebrow">Propuesta de valor</div>
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
                    <div class="eyebrow">Personalizacion</div>
                    <h2 class="fw-bold mt-2 mb-0">Productos independientes</h2>
                </div>
                <p class="text-secondary mb-0 max-copy">Seguridad para bikers, placas inteligentes para mascotas y personalizacion textil con sublimacion y DTF.</p>
            </div>
            <div class="row g-4 independent-products-grid">
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-dark product-card-biker" href="{{ route('services.show', 'biker-tag') }}">
                        <span class="badge text-bg-warning">Emergencia</span>
                        <h3>Biker Tag QR</h3>
                        <p>Dog tag para motociclistas con perfil medico, contactos y opcion para motoclubes.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-light product-card-dog" href="{{ route('services.show', 'dog-tags') }}">
                        <span class="badge text-bg-success">Mascotas</span>
                        <h3>Dog Tags QR</h3>
                        <p>Placas para mascota con perfil editable, WhatsApp y control de privacidad.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-light product-card-embroidery" href="#textil">
                        <span class="badge text-bg-light">Interactivo</span>
                        <h3>Sublimacion & DTF</h3>
                        <p>Sube tu logo, elige gorra, chamarra o playera y acomoda el diseño antes de cotizar.</p>
                        <span class="card-action">Abrir simulador <i class="bi bi-arrow-down-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3 independent-products-item">
                    <a class="product-card product-card-light product-card-laser" href="{{ route('services.laser') }}">
                        <span class="badge text-bg-danger">Laser</span>
                        <h3>Laser personalizado</h3>
                        <p>Logo o escritura en termos, carcasas, parches para playera, madera y acrilico.</p>
                        <span class="card-action">Ver configurador <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad embroidery-section" id="textil">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="sticky-copy glass-copy">
                        <div class="eyebrow">Textil y objetos personalizados</div>
                        <h2 class="fw-bold mt-2">Tu idea en prendas, termos, gorras y mas.</h2>
                        <p class="text-secondary">Puedes mandar a personalizar casi cualquier prenda u objeto. Si no sabes si conviene sublimacion, DTF, laser u otra tecnica, nosotros te ayudamos a elegir la mejor opcion segun material, color, cantidad y uso.</p>
                        <div class="embroidery-steps">
                            <div><i class="bi bi-upload"></i><span>Mandanos logo, texto, foto o referencia.</span></div>
                            <div><i class="bi bi-stars"></i><span>Revisamos material, color y acabado ideal.</span></div>
                            <div><i class="bi bi-whatsapp"></i><span>Te cotizamos la mejor tecnica antes de producir.</span></div>
                        </div>
                        <div class="textile-actions">
                            <a class="btn btn-dark" href="https://wa.me/525564442949?text={{ rawurlencode('Hola, quiero cotizar una prenda u objeto personalizado. Necesito ayuda para elegir entre sublimacion, DTF u otra tecnica.') }}" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-2"></i>Cotizar personalizacion
                            </a>
                            <a class="btn btn-outline-dark" href="#textil-preview">
                                <i class="bi bi-image me-2"></i>Probar mockup
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="embroidery-tool" id="textil-preview" data-embroidery-tool>
                        <div class="textile-tool-note">
                            <strong>Vista rapida</strong>
                            <span>Usa este mockup como guia. La cotizacion final se confirma por WhatsApp segun material, tamaño, cantidad y tecnica.</span>
                        </div>
                        <div class="embroidery-toolbar">
                            <label class="upload-tile" for="embroideryLogoInput">
                                <i class="bi bi-image"></i>
                                <span>
                                    <strong>Cargar logo</strong>
                                    <small>PNG, JPG o SVG</small>
                                </span>
                            </label>
                            <input class="visually-hidden" id="embroideryLogoInput" type="file" accept="image/*" data-logo-input>

                            <div class="garment-picker" role="group" aria-label="Elegir prenda">
                                <button class="garment-option active" type="button" data-garment="cap">
                                    <span class="garment-icon garment-icon-cap" aria-hidden="true"></span><span>Gorra</span>
                                </button>
                                <button class="garment-option" type="button" data-garment="jacket">
                                    <i class="bi bi-gem"></i><span>Chamarra</span>
                                </button>
                                <button class="garment-option" type="button" data-garment="shirt">
                                    <span class="garment-icon garment-icon-shirt" aria-hidden="true"></span><span>Playera</span>
                                </button>
                            </div>
                        </div>

                        <div class="mockup-stage" data-mockup-stage>
                            <img class="garment-preview" src="{{ asset('images/embroidery-cap.png') }}" alt="Mockup de gorra para sublimacion o DTF" loading="lazy" decoding="async" data-garment-preview>
                            <div class="embroidery-placeholder" data-logo-placeholder>
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Tu logo aqui</span>
                            </div>
                            <img class="logo-overlay" alt="Logo cargado para simular sublimacion o DTF" data-logo-preview hidden>
                            <div class="stitch-frame" aria-hidden="true"></div>
                        </div>

                        <div class="embroidery-controls">
                            <label>
                                <span>Tamaño</span>
                                <input type="range" min="12" max="42" value="24" data-logo-size>
                            </label>
                            <label>
                                <span>Rotacion</span>
                                <input type="range" min="-25" max="25" value="0" data-logo-rotate>
                            </label>
                            <button class="btn btn-dark" type="button" data-reset-logo>
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Centrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="row g-4 audience-grid">
                @foreach ([
                    'Restaurantes' => 'Bases QR, menu editable, WhatsApp, estadisticas y panel por sucursal.',
                    'Corporativo' => 'Kits, credenciales NFC, uniformes personalizados, portal interno y reposiciones por sede.',
                    'Eventos' => 'Invitaciones hibridas, RSVP, mapas, seating charts y recuerdos personalizados.',
                    'Maquila' => 'Produccion sin marca para agencias, imprentas, wedding planners y diseñadores.',
                ] as $title => $text)
                    <div class="col-md-6 audience-grid-item">
                        <div class="line-card">
                            <h3>{{ $title }}</h3>
                            <p>{{ $text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad launch-band" id="lanzamiento">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <div class="eyebrow">Cotiza sin vueltas</div>
                    <h2 class="fw-bold mt-2">Dinos que quieres fabricar y armamos una propuesta clara.</h2>
                    <div class="launch-actions">
                        <a class="btn btn-light" href="#catalogo"><i class="bi bi-bag-heart me-2"></i>Ver catalogo</a>
                        <a class="btn btn-outline-light" href="https://wa.me/525564442949?text={{ rawurlencode('Hola, quiero cotizar un producto personalizado con ForjaLab.') }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar</a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="timeline">
                        <div><strong>Producto</strong><span>Elige una pieza, paquete o idea especial.</span></div>
                        <div><strong>Cantidad</strong><span>Define si sera individual, set o pedido por volumen.</span></div>
                        <div><strong>Diseño</strong><span>Mandanos logo, texto, foto o referencia visual.</span></div>
                        <div><strong>Entrega</strong><span>Confirmamos costo, tiempo y detalles de produccion.</span></div>
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
            '.home-motion .timeline > div'
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
