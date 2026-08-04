@extends('layouts.app')

@section('title', 'ForjaLab | Taller de productos fisicos conectados')

@section('content')
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="glass-copy">
                    <div class="eyebrow mb-3">Personalizamos · creamos · conectamos</div>
                    <h1 class="display-4 fw-bold mb-4">Productos personalizados que conectan lo fisico con lo digital.</h1>
                    <p class="lead text-secondary mb-4">ForjaLab convierte ideas en productos reales: placas QR, Biker Tags, tarjetas NFC, piezas 3D, sublimacion, DTF y soluciones web desde una pieza hasta volumen.</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-dark btn-lg" href="#servicios"><i class="bi bi-grid-1x2-fill me-2"></i>Ver servicios</a>
                        <a class="btn btn-outline-dark btn-lg" href="{{ route('services.show', 'biker-tag') }}"><i class="bi bi-shield-check me-2"></i>Biker Tag</a>
                    </div>
                    <div class="hero-metrics">
                        <div><strong>1+</strong><span>pieza inicial</span></div>
                        <div><strong>QR/NFC</strong><span>perfiles y paneles</span></div>
                        <div><strong>CDMX</strong><span>produccion local</span></div>
                    </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img class="hero-image" src="{{ asset('images/forjalab-hero.png') }}" alt="Productos personalizados con QR, NFC, sublimacion, DTF e impresion 3D" fetchpriority="high" decoding="async">
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad" id="valor">
        <div class="container">
            <div class="row g-4 align-items-end mb-4">
                <div class="col-lg-7">
                    <div class="eyebrow">Propuesta de valor</div>
                    <h2 class="fw-bold mt-2">No vendemos solo objetos: forjamos producto mas sistema.</h2>
                </div>
                <div class="col-lg-5 text-secondary">Placa mas perfil, menu mas panel, credencial mas tarjeta virtual, uniforme mas portal de reposiciones.</div>
            </div>
            <div class="row g-3">
                @foreach ([
                    ['icon' => 'boxes', 'title' => 'Produccion hibrida', 'text' => 'Laser, 3D, sublimacion, DTF, QR, NFC y desarrollo web en un mismo flujo.'],
                    ['icon' => 'person-check', 'title' => 'Sin minimos elevados', 'text' => 'Atencion desde una pieza hasta campanas empresariales completas.'],
                    ['icon' => 'arrow-repeat', 'title' => 'Ingresos recurrentes', 'text' => 'Hosting, renovaciones, menus, soporte, dominios y reposiciones.'],
                    ['icon' => 'cpu', 'title' => 'Automatizacion interna', 'text' => 'Cotizacion, QR, archivos por lote, listas de nombres y seguimiento.'],
                ] as $item)
                    <div class="col-md-6 col-xl-3">
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
                    <div class="eyebrow">Landings iniciales</div>
                    <h2 class="fw-bold mt-2 mb-0">Mini productos independientes</h2>
                </div>
                <p class="text-secondary mb-0 max-copy">Empezamos con ofertas claras para validar rapido: seguridad para bikers, placas inteligentes para mascotas y personalizacion textil con sublimacion y DTF.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-xl-3">
                    <a class="product-card product-card-dark product-card-biker" href="{{ route('services.show', 'biker-tag') }}">
                        <span class="badge text-bg-warning">Emergencia</span>
                        <h3>Biker Tag QR</h3>
                        <p>Dog tag para motociclistas con perfil medico, contactos y opcion para motoclubes.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="product-card product-card-light product-card-dog" href="{{ route('services.show', 'dog-tags') }}">
                        <span class="badge text-bg-success">Mascotas</span>
                        <h3>Dog Tags QR</h3>
                        <p>Placas para mascota con perfil editable, WhatsApp y control de privacidad.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="product-card product-card-light product-card-embroidery" href="#textil">
                        <span class="badge text-bg-light">Interactivo</span>
                        <h3>Sublimacion & DTF</h3>
                        <p>Sube tu logo, elige gorra, chamarra o playera y acomoda el diseno antes de cotizar.</p>
                        <span class="card-action">Abrir simulador <i class="bi bi-arrow-down-right"></i></span>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
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

    <section class="section-pad catalog-section" id="catalogo">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow">Mini catalogo de temporada</div>
                    <h2 class="fw-bold mt-2 mb-0">Productos listos para regalar, vender o personalizar.</h2>
                </div>
                <p class="text-secondary mb-0 max-copy">Piezas seleccionadas para temporadas, eventos, kits empresariales y regalos con nombre, logo o detalle especial.</p>
            </div>

            @if ($featuredCatalogProduct)
                @include('catalog.partials.product-card', ['product' => $featuredCatalogProduct])
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

    <section class="section-pad embroidery-section" id="textil">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="sticky-copy glass-copy">
                        <div class="eyebrow">Sublimacion y DTF</div>
                        <h2 class="fw-bold mt-2">Carga tu logo y arma un mockup rapido antes de cotizar.</h2>
                        <p class="text-secondary">Aplicamos sublimacion para prendas claras de poliester y DTF para textiles de algodon, mezclas o colores intensos. Preparamos tu archivo, revisamos tamano, posicion y acabado para producir piezas limpias y listas para entrega.</p>
                        <div class="embroidery-steps">
                            <div><i class="bi bi-upload"></i><span>Sube tu logo o imagen.</span></div>
                            <div><i class="bi bi-bounding-box"></i><span>Elige prenda y acomoda el diseno.</span></div>
                            <div><i class="bi bi-fire"></i><span>Cotizamos sublimacion, DTF, muestra y produccion.</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="embroidery-tool" data-embroidery-tool>
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
                                    <i class="bi bi-circle-square"></i><span>Gorra</span>
                                </button>
                                <button class="garment-option" type="button" data-garment="jacket">
                                    <i class="bi bi-gem"></i><span>Chamarra</span>
                                </button>
                                <button class="garment-option" type="button" data-garment="shirt">
                                    <i class="bi bi-square"></i><span>Playera</span>
                                </button>
                            </div>
                        </div>

                        <div class="mockup-stage" data-mockup-stage>
                            <img class="garment-preview" src="{{ asset('images/embroidery-cap.png') }}" alt="Mockup de gorra para sublimacion o DTF" data-garment-preview>
                            <div class="embroidery-placeholder" data-logo-placeholder>
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Tu logo aqui</span>
                            </div>
                            <img class="logo-overlay" alt="Logo cargado para simular sublimacion o DTF" data-logo-preview hidden>
                            <div class="stitch-frame" aria-hidden="true"></div>
                        </div>

                        <div class="embroidery-controls">
                            <label>
                                <span>Tamano</span>
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
            <div class="row g-4">
                @foreach ([
                    'Restaurantes' => 'Bases QR, menu editable, WhatsApp, estadisticas y panel por sucursal.',
                    'Corporativo' => 'Kits, credenciales NFC, uniformes personalizados, portal interno y reposiciones por sede.',
                    'Eventos' => 'Invitaciones hibridas, RSVP, mapas, seating charts y recuerdos personalizados.',
                    'Maquila' => 'Produccion sin marca para agencias, imprentas, wedding planners y disenadores.',
                ] as $title => $text)
                    <div class="col-md-6">
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
                    <div class="eyebrow">Ruta de 90 dias</div>
                    <h2 class="fw-bold mt-2">Lanzar simple, medir rapido y convertir en recurrencia.</h2>
                </div>
                <div class="col-lg-7">
                    <div class="timeline">
                        <div><strong>Dias 1-15</strong><span>Marca, 3 productos, 10 muestras, MVP QR, costos reales.</span></div>
                        <div><strong>Dias 16-30</strong><span>Redes, catalogo, WhatsApp, veterinarias, negocios y motoclubes.</span></div>
                        <div><strong>Meses 2-3</strong><span>Ajuste de precios, paquetes, alianzas, NFC y renovaciones.</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
