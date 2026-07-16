@extends('layouts.app')

@section('title', 'LabCustom | Taller de productos fisicos conectados')

@section('content')
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="eyebrow mb-3">Diseno · fabricacion · software</div>
                    <h1 class="display-4 fw-bold mb-4">Productos personalizados que conectan lo fisico con lo digital.</h1>
                    <p class="lead text-secondary mb-4">Un taller en CDMX para crear placas QR, Biker Tags, tarjetas NFC, piezas 3D, bordados y soluciones web desde una pieza hasta volumen.</p>
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
                <div class="col-lg-6">
                    <img class="hero-image" src="{{ asset('images/connected-products-hero.png') }}" alt="Productos personalizados con QR, NFC, bordado e impresion 3D">
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad" id="valor">
        <div class="container">
            <div class="row g-4 align-items-end mb-4">
                <div class="col-lg-7">
                    <div class="eyebrow">Propuesta de valor</div>
                    <h2 class="fw-bold mt-2">No vendemos solo objetos: entregamos producto mas sistema.</h2>
                </div>
                <div class="col-lg-5 text-secondary">Placa mas perfil, menu mas panel, credencial mas tarjeta virtual, uniforme mas portal de reposiciones.</div>
            </div>
            <div class="row g-3">
                @foreach ([
                    ['icon' => 'boxes', 'title' => 'Produccion hibrida', 'text' => 'Laser, 3D, bordado, QR, NFC y desarrollo web en un mismo flujo.'],
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
                <p class="text-secondary mb-0 max-copy">Empezamos con ofertas claras para validar rapido: seguridad para bikers y placas inteligentes para mascotas.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <a class="product-card product-card-dark" href="{{ route('services.show', 'biker-tag') }}">
                        <span class="badge text-bg-warning">Emergencia</span>
                        <h3>Biker Tag QR</h3>
                        <p>Dog tag para motociclistas con perfil medico, contactos y opcion para motoclubes.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
                <div class="col-lg-6">
                    <a class="product-card product-card-light" href="{{ route('services.show', 'dog-tags') }}">
                        <span class="badge text-bg-success">Mascotas</span>
                        <h3>Dog Tags QR</h3>
                        <p>Placas para mascota con perfil editable, WhatsApp y control de privacidad.</p>
                        <span class="card-action">Ver landing <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="row g-4">
                @foreach ([
                    'Restaurantes' => 'Bases QR, menu editable, WhatsApp, estadisticas y panel por sucursal.',
                    'Corporativo' => 'Kits, credenciales NFC, uniformes, portal interno y reposiciones por sede.',
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
