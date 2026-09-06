@extends('layouts.app')
@section('title', 'Aviso de privacidad | ForjaLab')
@section('meta_description', 'Aviso de privacidad de ForjaLab y uso de datos de Google Drive.')
@section('canonical', route('privacy.policy'))
@section('content')
<section class="legal-page">
    <div class="container">
        <a class="legal-back" href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Volver a ForjaLab</a>
        <header class="legal-header"><span class="eyebrow">Transparencia y seguridad</span><h1>Aviso de privacidad</h1><p>Última actualización: 6 de septiembre de 2026</p></header>
        <div class="legal-content panel-card">
            <h2>Responsable</h2>
            <p>ForjaLab, con sitio web en <strong>forjalab.com.mx</strong>, es responsable del tratamiento de los datos utilizados por sus servicios digitales. Para preguntas o solicitudes relacionadas con privacidad puedes escribir a <a href="mailto:forjalabbygamefix@gmail.com">forjalabbygamefix@gmail.com</a>.</p>

            <h2>Información que tratamos</h2>
            <p>Podemos tratar datos proporcionados directamente por clientes y administradores, como nombre, correo electrónico, teléfono, información de pedidos y archivos necesarios para prestar nuestros servicios.</p>

            <h2>Uso de Google Drive</h2>
            <p>El módulo administrativo “ForjaLab Drive Gallery” solicita autorización a una cuenta de Google para visualizar, organizar, descargar y cargar archivos dentro de la galería de Google Drive configurada por ForjaLab.</p>
            <ul>
                <li>La autorización se utiliza exclusivamente para operar la galería seleccionada desde el sistema administrativo.</li>
                <li>Los archivos permanecen almacenados en la cuenta de Google Drive autorizada.</li>
                <li>Los tokens de acceso y renovación se almacenan cifrados y no se muestran a los usuarios.</li>
                <li>No vendemos datos de Google ni los utilizamos para publicidad.</li>
                <li>No compartimos datos obtenidos de Google con terceros, salvo cuando sea necesario por obligación legal o para proteger la seguridad del servicio.</li>
            </ul>
            <p>El uso de información recibida de las APIs de Google cumple con la <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">Política de Datos de Usuario de los Servicios API de Google</a>, incluidos sus requisitos de Uso Limitado.</p>

            <h2>Finalidades</h2>
            <p>Usamos la información para administrar pedidos, atender clientes, entregar productos, operar la galería interna, mantener la seguridad del sistema y cumplir obligaciones legales.</p>

            <h2>Conservación y seguridad</h2>
            <p>Conservamos la información sólo durante el tiempo necesario para las finalidades descritas. Aplicamos controles de acceso, cifrado de credenciales y permisos administrativos para reducir riesgos de acceso no autorizado.</p>

            <h2>Revocación y eliminación</h2>
            <p>La autorización de Google puede revocarse desde la configuración de seguridad de la cuenta de Google. También puedes solicitar la desconexión de Google Drive o la eliminación de los tokens almacenados escribiendo a <a href="mailto:forjalabbygamefix@gmail.com">forjalabbygamefix@gmail.com</a>.</p>

            <h2>Cambios al aviso</h2>
            <p>Podemos actualizar este aviso cuando cambien las funciones del sistema o las obligaciones aplicables. La versión vigente se publicará en esta misma dirección.</p>
        </div>
    </div>
</section>
@include('partials.legal-styles')
@endsection
