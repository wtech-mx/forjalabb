@extends('layouts.app')

@section('title', 'Laser personalizado | ForjaLab')
@section('meta_description', 'Corte y grabado laser personalizado en CDMX para termos, textiles, accesorios y regalos. Cotiza materiales, diseño y acabado con ForjaLab.')
@section('seo_image', asset('images/laser-hero.png'))

@section('content')
    <section class="laser-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="glass-copy">
                        <div class="eyebrow">Laser y personalizacion a medida</div>
                        <h1 class="display-5 fw-bold mt-3 mb-3">Cada pieza se cotiza segun tu idea, material y acabado.</h1>
                        <p class="lead text-secondary mb-4">Grabamos y cortamos piezas personalizadas para regalos, marcas, eventos y productos de venta. Si no sabes si conviene laser, DTF, sublimacion u otra tecnica, mandanos tu idea y te guiamos con la mejor opcion.</p>
                        <div class="d-flex flex-wrap gap-2 laser-hero-actions">
                            <a class="btn btn-dark btn-lg" href="https://wa.me/525564442949?text=Hola%2C%20quiero%20cotizar%20un%20grabado%20o%20corte%20laser%20personalizado.%20Necesito%20ayuda%20para%20elegir%20la%20mejor%20opcion." target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar por WhatsApp</a>
                            <a class="btn btn-outline-dark btn-lg" href="#laser-configurador"><i class="bi bi-magic me-2"></i>Probar vista rapida</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img class="hero-image" src="{{ asset('images/laser-hero.png') }}" alt="Grabado laser personalizado en termo, carcasa y parche" loading="eager" fetchpriority="high" decoding="async">
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="row g-3 laser-feature-grid">
                @foreach ([
                    ['icon' => 'cup-hot', 'title' => 'Termos y tumblers', 'text' => 'Nombres, logos, frases o patrones en piezas compatibles. Revisamos tu termo antes de confirmar.'],
                    ['icon' => 'phone', 'title' => 'Carcasas y accesorios', 'text' => 'Grabado fino en superficies rigidas, mate o piezas especiales segun el material.'],
                    ['icon' => 'tag', 'title' => 'Parches y detalles', 'text' => 'Piel, sinteticos o aplicaciones para prendas, gorras, bolsas y productos personalizados.'],
                    ['icon' => 'layers', 'title' => 'Madera y acrilico', 'text' => 'Corte, marcado, placas, letreros, displays y detalles para regalos o eventos.'],
                ] as $item)
                    <div class="col-md-6 col-xl-3 laser-feature-item">
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

    <section class="section-pad laser-config-section" id="laser-configurador">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="sticky-copy glass-copy">
                        <div class="eyebrow">Vista previa y asesoria</div>
                        <h2 class="fw-bold mt-2">Prueba una idea y despues la aterrizamos contigo.</h2>
                        <p class="text-secondary">El mockup es una referencia visual. Para cotizar bien necesitamos saber que objeto quieres personalizar, material, medidas aproximadas, cantidad y si ya tienes logo o diseño. Con eso te decimos si laser es la mejor tecnica o si conviene otra opcion.</p>
                        <div class="laser-notes">
                            <div><strong>1. Idea</strong><span>Logo, nombre, frase, foto o referencia.</span></div>
                            <div><strong>2. Objeto</strong><span>Termo, placa, carcasa, madera, acrilico u otra pieza.</span></div>
                            <div><strong>3. Cotizacion</strong><span>Te recomendamos tecnica, acabado y precio antes de producir.</span></div>
                        </div>
                        <a class="btn btn-dark w-100 mt-3" href="https://wa.me/525564442949?text={{ rawurlencode('Hola, quiero cotizar una pieza personalizada. Tengo dudas si conviene laser u otra tecnica.') }}" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i>Mandar mi idea para cotizar
                        </a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="laser-tool" data-laser-tool>
                        <div class="textile-tool-note">
                            <strong>Personalizado, no generico</strong>
                            <span>Cada producto se revisa antes de producir. Te ayudamos a elegir material, tecnica y acabado para que el resultado se vea limpio y dure.</span>
                        </div>
                        <div class="laser-toolbar">
                            <label class="upload-tile" for="laserLogoInput">
                                <i class="bi bi-image"></i>
                                <span>
                                    <strong>Cargar logo</strong>
                                    <small>Opcional</small>
                                </span>
                            </label>
                            <input class="visually-hidden" id="laserLogoInput" type="file" accept="image/*" data-laser-logo-input>

                            <label class="laser-text-input">
                                <span>Texto a grabar</span>
                                <input class="form-control" type="text" maxlength="28" value="FORJALAB" data-laser-text>
                            </label>
                        </div>

                        <div class="laser-material-picker" role="group" aria-label="Elegir superficie laser">
                            <button class="laser-option active" type="button" data-laser-surface="thermo"><i class="bi bi-cup-hot"></i><span>Termo</span></button>
                            <button class="laser-option" type="button" data-laser-surface="case"><i class="bi bi-phone"></i><span>Carcasa</span></button>
                            <button class="laser-option" type="button" data-laser-surface="patch"><i class="bi bi-square"></i><span>Playera/parche</span></button>
                        </div>

                        <div class="laser-stage" data-laser-stage>
                            <img class="laser-surface-preview" src="{{ asset('images/laser-thermo.png') }}" alt="Mockup de termo para grabado laser" loading="lazy" decoding="async" data-laser-preview>
                            <div class="laser-mark" data-laser-mark>
                                <img alt="Logo cargado para simular grabado laser" data-laser-logo-preview hidden>
                                <span data-laser-text-preview>FORJALAB</span>
                            </div>
                            <div class="laser-frame" aria-hidden="true"></div>
                        </div>

                        <div class="laser-controls">
                            <label>
                                <span>Tamano</span>
                                <input type="range" min="12" max="44" value="24" data-laser-size>
                            </label>
                            <label>
                                <span>Rotacion</span>
                                <input type="range" min="-25" max="25" value="0" data-laser-rotate>
                            </label>
                            <button class="btn btn-dark" type="button" data-laser-reset>
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Centrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
