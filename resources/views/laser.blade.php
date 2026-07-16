@extends('layouts.app')

@section('title', 'Laser personalizado | ForjaLab')

@section('content')
    <section class="laser-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="glass-copy">
                        <div class="eyebrow">Grabado y corte laser</div>
                        <h1 class="display-5 fw-bold mt-3 mb-3">Personaliza termos, carcasas, playeras y piezas especiales.</h1>
                        <p class="lead text-secondary mb-4">Grabamos logos, nombres, frases y patrones sobre metal, madera, piel, acrilico y algunos recubrimientos. Ideal para regalos, marca interna, eventos y piezas de venta.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-dark btn-lg" href="#laser-configurador"><i class="bi bi-magic me-2"></i>Probar configurador</a>
                            <a class="btn btn-outline-dark btn-lg" href="https://wa.me/?text=Hola%2C%20quiero%20cotizar%20grabado%20laser%20personalizado" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img class="hero-image" src="{{ asset('images/laser-hero.png') }}" alt="Grabado laser personalizado en termo, carcasa y parche">
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="row g-3">
                @foreach ([
                    ['icon' => 'cup-hot', 'title' => 'Termos y tumblers', 'text' => 'Nombres, logos, frases o patrones en acero y recubrimientos compatibles.'],
                    ['icon' => 'phone', 'title' => 'Carcasas', 'text' => 'Grabado fino para fundas rigidas o superficies mate con acabado personalizado.'],
                    ['icon' => 'tag', 'title' => 'Playeras y parches', 'text' => 'Parches de piel o sinteticos grabados para coser, pegar o integrar a prendas.'],
                    ['icon' => 'layers', 'title' => 'Madera y acrilico', 'text' => 'Corte, marcado y piezas decorativas para regalos, eventos y displays.'],
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

    <section class="section-pad laser-config-section" id="laser-configurador">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="sticky-copy glass-copy">
                        <div class="eyebrow">Simulador laser</div>
                        <h2 class="fw-bold mt-2">Carga tu logo o escribe una frase y prueba superficies.</h2>
                        <p class="text-secondary">Este preview ayuda a decidir posicion, tamano y tipo de producto. El acabado final depende del material, potencia, velocidad y preparacion del archivo.</p>
                        <div class="laser-notes">
                            <div><strong>Logo</strong><span>PNG, JPG o SVG para simular grabado.</span></div>
                            <div><strong>Texto</strong><span>Nombre, frase corta o iniciales.</span></div>
                            <div><strong>Material</strong><span>Termo, carcasa o parche sobre playera.</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="laser-tool" data-laser-tool>
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
                            <img class="laser-surface-preview" src="{{ asset('images/laser-thermo.png') }}" alt="Mockup de termo para grabado laser" data-laser-preview>
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
