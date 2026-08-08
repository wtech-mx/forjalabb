@extends('layouts.public')

@section('title', 'Biker Tag de emergencia | ForjaLab')

@section('content')
    <section class="qr-landing biker-qr">
        <div class="container">
            <div class="qr-shell">
                <div class="qr-hero">
                    <div>
                        <span class="qr-kicker"><i class="bi bi-shield-fill-plus"></i> Biker emergency ID</span>
                        <h1>{{ $tag->display_name }}</h1>
                        <p>Informacion critica para auxiliar y contactar a su red de emergencia.</p>
                    </div>
                    <span class="status-pill {{ $tag->is_active ? 'active' : 'inactive' }}">{{ $tag->is_active ? 'Activo' : 'Pausado' }}</span>
                </div>

                @unless ($tag->is_active)
                    <div class="alert alert-light border">Este Biker Tag no esta activo. Verifica con el responsable o con ForjaLab.</div>
                @endunless

                <div class="scan-alert-box" data-scan-status>
                    <i class="bi bi-crosshair"></i>
                    <span>Comparte la ubicacion GPS para avisar a los contactos de emergencia.</span>
                    <button class="scan-location-button" type="button" data-scan-trigger>Enviar ubicacion</button>
                </div>

                <div class="qr-alert-grid">
                    <div class="qr-alert-item blood">
                        <span>Tipo de sangre</span>
                        <strong>{{ $tag->blood_type ?: 'N/D' }}</strong>
                    </div>
                    <div class="qr-alert-item">
                        <span>Donador</span>
                        <strong>{{ $tag->is_blood_donor ? 'Si' : 'No' }}</strong>
                    </div>
                    <div class="qr-alert-item">
                        <span>Moto / vehiculo</span>
                        <strong>{{ $tag->vehicle ?: 'No capturado' }}</strong>
                    </div>
                    <div class="qr-alert-item">
                        <span>Placas</span>
                        <strong>{{ $tag->motorcycle_plate ?: 'No capturadas' }}</strong>
                    </div>
                </div>

                <div class="qr-glass-panel">
                    <h2><i class="bi bi-heart-pulse-fill"></i> Datos medicos</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="qr-note">
                                <span>Alergias</span>
                                <strong>{{ $tag->allergies ?: 'Sin alergias registradas' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="qr-note">
                                <span>Notas</span>
                                <strong>{{ $tag->medical_notes ?: 'Sin notas medicas capturadas' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="qr-glass-panel">
                    <h2><i class="bi bi-shield-check"></i> Seguros y atencion</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="qr-note">
                                <span>Seguro del vehiculo</span>
                                @if ($tag->has_vehicle_insurance)
                                    <strong>Poliza: {{ $tag->vehicle_insurance_policy }}</strong>
                                    <small>Vigente hasta: {{ optional($tag->vehicle_insurance_expires_at)->format('d/m/Y') ?: 'No capturado' }}</small>
                                @else
                                    <strong>No registrado</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="qr-note">
                                <span>Servicio publico de salud</span>
                                @if ($tag->has_public_health_insurance)
                                    <strong>{{ $tag->public_health_provider_label }}</strong>
                                    <small>Numero: {{ $tag->public_health_number }}</small>
                                @else
                                    <strong>No registrado</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if ($tag->public_notes)
                    <div class="qr-message">{{ $tag->public_notes }}</div>
                @endif

                <div class="qr-contact-strip">
                    @if ($tag->owner_phone)
                        <a href="tel:{{ $tag->owner_phone }}"><i class="bi bi-telephone-fill"></i><span>Llamar<br><strong>{{ $tag->owner_name ?: 'Contacto principal' }}</strong><small>{{ $tag->owner_phone }}</small></span></a>
                        <a class="whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', $tag->owner_phone) }}?text=Hola%2C%20escanee%20el%20Biker%20Tag%20de%20{{ urlencode($tag->display_name) }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i><span>WhatsApp<br><strong>Avisar ahora</strong></span></a>
                    @endif
                    @if ($tag->secondary_contact_phone)
                        <a href="tel:{{ $tag->secondary_contact_phone }}"><i class="bi bi-telephone-plus-fill"></i><span>Secundario<br><strong>{{ $tag->secondary_contact_name ?: 'Contacto 2' }}</strong><small>{{ $tag->secondary_contact_phone }}</small></span></a>
                    @endif
                </div>

                <p class="qr-disclaimer">Este perfil ayuda a contactar al responsable. No sustituye servicios medicos ni de emergencia.</p>
            </div>
        </div>
    </section>

    @include('tags.partials.scan-script', ['tag' => $tag])
@endsection
