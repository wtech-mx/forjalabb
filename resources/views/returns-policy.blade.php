@extends('layouts.app')
@section('title', 'Politica de cambios y devoluciones | ForjaLab')
@section('meta_description', 'Consulta la politica de cambios, devoluciones y garantia de productos personalizados de ForjaLab en Ciudad de Mexico.')
@section('canonical', route('returns.policy'))
@section('content')
<section class="returns-hero">
    <div class="container">
        <a class="returns-back" href="{{ route('home') }}"><i class="bi bi-arrow-left"></i>Volver a ForjaLab</a>
        <div class="returns-hero-grid">
            <div><span class="eyebrow">Compra con claridad</span><h1>Politica de cambios y devoluciones</h1><p>Fabricamos productos personalizados especialmente para cada cliente. Aqui explicamos de forma sencilla cuando podemos atender un cambio, una garantia o una devolucion.</p><small>Ultima actualizacion: 28 de agosto de 2026</small></div>
            <div class="returns-hero-icon"><i class="bi bi-shield-check"></i><strong>Atencion directa</strong><span>Productos defectuosos entregados en CDMX</span></div>
        </div>
    </div>
</section>

<section class="returns-content"><div class="container">
    <div class="returns-notice"><i class="bi bi-stars"></i><div><strong>Productos hechos para ti</strong><p>Por tratarse de articulos personalizados o fabricados bajo pedido, no aceptamos cambios ni devoluciones por preferencia personal, cambio de opinion o errores en datos, medidas, colores o diseños que hayan sido aprobados por el cliente.</p></div></div>

    <div class="returns-grid">
        <article class="returns-card no-change"><span><i class="bi bi-x-circle"></i></span><h2>Casos sin cambio o devolucion</h2><ul><li>Cambio de opinion después de aprobar el pedido.</li><li>Errores de escritura, nombres, fechas o datos proporcionados por el cliente.</li><li>Variaciones razonables de color entre una pantalla y el producto terminado.</li><li>Desgaste, golpes, humedad o uso inadecuado después de la entrega.</li><li>Productos personalizados que coincidan con el diseño y especificaciones aprobadas.</li></ul></article>
        <article class="returns-card accepted"><span><i class="bi bi-check-circle"></i></span><h2>Productos defectuosos en CDMX</h2><p>Atendemos productos entregados en Ciudad de Mexico cuando presenten un defecto de fabricacion, no correspondan con las especificaciones aprobadas o lleguen dañados.</p><p>Después de revisar el caso, ofreceremos la solucion aplicable, que puede incluir reparacion, reposicion, correccion o devolucion del importe, respetando los derechos que correspondan a la persona consumidora.</p></article>
    </div>

    <div class="returns-process panel-card">
        <div><span class="eyebrow">Proceso de atencion</span><h2>¿Tu producto presenta un defecto?</h2><p>Contactanos tan pronto como detectes el problema. Conserva el producto y su empaque mientras revisamos el caso.</p></div>
        <ol><li><b>1</b><span><strong>Envia tu folio</strong><small>Comparte el numero de pedido y una descripcion del problema.</small></span></li><li><b>2</b><span><strong>Adjunta evidencia</strong><small>Incluye fotografias o video donde pueda apreciarse el defecto.</small></span></li><li><b>3</b><span><strong>Revision</strong><small>Confirmaremos si procede la garantia y te indicaremos la entrega o recoleccion en CDMX.</small></span></li><li><b>4</b><span><strong>Solucion</strong><small>Acordaremos la reparacion, reposicion, correccion o devolucion que corresponda.</small></span></li></ol>
    </div>

    <div class="returns-details">
        <article><i class="bi bi-clock-history"></i><div><h3>Plazo de garantia</h3><p>La garantia por defectos de fabricacion sera de al menos 90 dias naturales contados desde la entrega, sin perjuicio de los plazos o derechos adicionales establecidos por la legislacion aplicable.</p></div></article>
        <article><i class="bi bi-geo-alt-fill"></i><div><h3>Cobertura operativa</h3><p>Esta politica comercial aplica a productos vendidos y entregados por ForjaLab dentro de Ciudad de Mexico. La revision fisica, entrega o recoleccion se coordina previamente con nuestro equipo.</p></div></article>
        <article><i class="bi bi-receipt"></i><div><h3>Comprobante</h3><p>Solicitaremos el folio, comprobante de compra o datos suficientes para localizar el pedido y verificar sus especificaciones.</p></div></article>
    </div>

    <div class="returns-contact"><div><span>¿Necesitas reportar un problema?</span><h2>Estamos para ayudarte</h2><p>Escribenos con tu folio y evidencia del producto para iniciar la revision.</p></div><a class="btn btn-success btn-lg" href="https://wa.me/525564442949?text=Hola%2C%20quiero%20reportar%20un%20problema%20con%20un%20producto.%20Mi%20folio%20es%3A" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp</a></div>
    <p class="returns-legal">Esta politica no limita los derechos irrenunciables reconocidos por la Ley Federal de Proteccion al Consumidor ni otras disposiciones aplicables.</p>
</div></section>
@endsection
