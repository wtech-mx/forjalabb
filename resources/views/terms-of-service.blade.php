@extends('layouts.app')
@section('title', 'Términos y condiciones | ForjaLab')
@section('meta_description', 'Términos y condiciones de uso de los servicios digitales de ForjaLab.')
@section('canonical', route('terms.service'))
@section('content')
<section class="legal-page">
    <div class="container">
        <a class="legal-back" href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Volver a ForjaLab</a>
        <header class="legal-header"><span class="eyebrow">Condiciones de uso</span><h1>Términos y condiciones</h1><p>Última actualización: 6 de septiembre de 2026</p></header>
        <div class="legal-content panel-card">
            <h2>Aceptación</h2>
            <p>Al utilizar forjalab.com.mx y sus herramientas administrativas aceptas estos términos. Si no estás de acuerdo, no debes utilizar los servicios.</p>

            <h2>Servicios</h2>
            <p>ForjaLab ofrece productos personalizados y herramientas digitales para administrar clientes, pedidos, archivos y procesos relacionados con su operación. Las características pueden cambiar para mejorar el servicio o su seguridad.</p>

            <h2>Acceso administrativo</h2>
            <p>Las funciones privadas sólo pueden ser utilizadas por personas autorizadas. Cada usuario es responsable de proteger sus credenciales, utilizar el sistema de forma legítima y avisar si detecta un acceso no autorizado.</p>

            <h2>Conexión con Google Drive</h2>
            <p>Un administrador autorizado puede conectar una cuenta de Google Drive para gestionar la galería definida por ForjaLab. La persona que autoriza declara tener permiso para usar esa cuenta y sus archivos. El acceso puede revocarse desde la cuenta de Google.</p>

            <h2>Uso permitido</h2>
            <p>No está permitido intentar vulnerar el sistema, acceder a información sin autorización, cargar contenido ilícito o malicioso, interferir con el servicio ni utilizarlo de forma que infrinja derechos de terceros.</p>

            <h2>Archivos y respaldos</h2>
            <p>Los usuarios autorizados son responsables de verificar los archivos que cargan y de mantener los respaldos que consideren necesarios. La disponibilidad de Google Drive también depende de los servicios proporcionados por Google.</p>

            <h2>Propiedad intelectual</h2>
            <p>Las marcas, diseños, textos, software y materiales de ForjaLab están protegidos por la legislación aplicable. Los archivos de clientes y terceros conservan la titularidad que legalmente les corresponda.</p>

            <h2>Limitación y cambios</h2>
            <p>Procuramos mantener los servicios disponibles y seguros, pero pueden existir interrupciones por mantenimiento, fallas técnicas o servicios externos. Estos términos podrán actualizarse y la versión vigente será la publicada en esta página.</p>

            <h2>Contacto</h2>
            <p>Para dudas sobre estos términos escribe a <a href="mailto:forjalabbygamefix@gmail.com">forjalabbygamefix@gmail.com</a>.</p>
        </div>
    </div>
</section>
@include('partials.legal-styles')
@endsection
