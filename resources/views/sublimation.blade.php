@extends('layouts.app')

@section('title', 'Sublimación y personalización textil en CDMX | ForjaLab')
@section('meta_description', 'Carga tu logo y visualízalo en gorras, playeras y chamarras. Sublimación, DTF y personalización textil con ForjaLab en CDMX.')
@section('canonical', route('services.sublimation'))
@section('seo_image', asset('images/embroidery-shirt.png'))

@section('content')
<div class="sublimation-page">
    <section class="sublimation-hero">
        <div class="container"><div class="row align-items-center g-5">
            <div class="col-lg-6"><span class="sublimation-kicker"><i class="bi bi-stars"></i> Tu marca, lista para vestir</span><h1>Sube tu logo.<br><em>Imagínalo puesto.</em></h1><p>No necesitas adivinar cómo se verá. Carga tu diseño, elige una prenda y crea una vista previa antes de cotizar.</p><div class="d-flex flex-wrap gap-2"><a class="btn btn-light btn-lg" href="#simulador"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Probar mi logo</a><a class="btn btn-outline-light btn-lg" href="https://wa.me/525564442949?text={{ rawurlencode('Hola, quiero cotizar sublimación o DTF para una prenda personalizada.') }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar</a></div><div class="sublimation-trust"><span><i class="bi bi-check-circle-fill"></i> Sin registro</span><span><i class="bi bi-check-circle-fill"></i> Vista inmediata</span><span><i class="bi bi-check-circle-fill"></i> Tu imagen no se sube al servidor</span></div></div>
            <div class="col-lg-6"><div class="sublimation-hero-art"><img src="{{ asset('images/embroidery-shirt.png') }}" alt="Playera personalizada mediante sublimación o DTF" width="720" height="720"><span class="floating-tech"><i class="bi bi-fire"></i><strong>Color que destaca</strong><small>Sublimación · DTF</small></span></div></div>
        </div></div>
    </section>

    <section class="section-pad sublimation-studio" id="simulador"><div class="container"><div class="sublimation-heading text-center"><span>Estudio interactivo</span><h2>Hazlo tuyo en segundos.</h2><p>Usa un PNG transparente para obtener el resultado más realista.</p></div>
        <div class="embroidery-tool sublimation-tool" data-embroidery-tool>
            <div class="textile-tool-note"><strong>Tu vista previa</strong><span>Esta simulación es una guía visual. Ajustaremos tamaño, color y técnica antes de producir.</span></div>
            <div class="embroidery-toolbar"><label class="upload-tile" for="sublimationLogoInput"><i class="bi bi-cloud-arrow-up-fill"></i><span><strong>Cargar mi logo</strong><small>PNG, JPG o SVG</small></span></label><input class="visually-hidden" id="sublimationLogoInput" type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-logo-input>
                <div class="garment-picker" role="group" aria-label="Elegir prenda"><button class="garment-option active" type="button" data-garment="cap"><span class="garment-icon garment-icon-cap"></span><span>Gorra</span></button><button class="garment-option" type="button" data-garment="jacket"><i class="bi bi-gem"></i><span>Chamarra</span></button><button class="garment-option" type="button" data-garment="shirt"><span class="garment-icon garment-icon-shirt"></span><span>Playera</span></button></div>
            </div>
            <div class="mockup-stage" data-mockup-stage><img class="garment-preview" src="{{ asset('images/embroidery-cap.png') }}" alt="Vista previa de prenda personalizada" data-garment-preview><div class="embroidery-placeholder" data-logo-placeholder><i class="bi bi-cloud-arrow-up-fill"></i><span>Tu logo aquí</span></div><img class="logo-overlay" alt="Logo cargado sobre la prenda" data-logo-preview hidden><div class="stitch-frame"></div></div>
            <div class="embroidery-controls"><label><span>Tamaño</span><input type="range" min="12" max="42" value="24" data-logo-size></label><label><span>Rotación</span><input type="range" min="-25" max="25" value="0" data-logo-rotate></label><button class="btn btn-dark" type="button" data-reset-logo><i class="bi bi-arrow-counterclockwise me-2"></i>Centrar</button></div>
            <div class="sublimation-tool-cta"><div><strong>¿Te gustó cómo se ve?</strong><small>Envíanos tu archivo y dinos cantidad, prenda y medidas.</small></div><a class="btn btn-success" href="https://wa.me/525564442949?text={{ rawurlencode('Hola, ya probé mi logo en el simulador de sublimación y quiero cotizar.') }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Cotizar este diseño</a></div>
        </div>
    </div></section>

    <section class="section-pad sublimation-techniques"><div class="container"><div class="row g-4"><div class="col-lg-4"><div class="technique-card orange"><i class="bi bi-sun-fill"></i><span>Colores vibrantes</span><h3>Sublimación</h3><p>Ideal para prendas claras con poliéster y objetos preparados. La tinta se integra al material y no deja relieve.</p></div></div><div class="col-lg-4"><div class="technique-card dark"><i class="bi bi-layers-fill"></i><span>Versátil y resistente</span><h3>DTF</h3><p>Perfecto para algodón, prendas oscuras, uniformes y diseños a todo color con gran definición.</p></div></div><div class="col-lg-4"><div class="technique-card green"><i class="bi bi-patch-check-fill"></i><span>Te ayudamos a elegir</span><h3>La técnica correcta</h3><p>Revisamos material, color, uso y cantidad para recomendar el acabado que realmente te conviene.</p></div></div></div></div></section>

    <section class="section-pad sublimation-final"><div class="container text-center"><span class="eyebrow">Una pieza o todo un equipo</span><h2>Tu idea merece salir del archivo.</h2><p>Playeras, gorras, uniformes, termos y proyectos especiales.</p><a class="btn btn-dark btn-lg" href="#simulador"><i class="bi bi-magic me-2"></i>Crear otra vista</a></div></section>
</div>
@endsection
