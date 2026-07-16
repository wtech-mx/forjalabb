@extends('layouts.public')

@section('title', 'Dog Tag QR | LabCustom')

@section('content')
    <section class="qr-landing dog-qr">
        <div class="container">
            <div class="qr-shell">
                <div class="qr-hero dog">
                    <div>
                        <span class="qr-kicker"><i class="bi bi-geo-alt-fill"></i> Mascota identificada</span>
                        <h1>{{ $tag->display_name }}</h1>
                        <p>Ayudanos a regresar a casa. Este QR conecta con su responsable.</p>
                    </div>
                    <span class="status-pill {{ $tag->is_active ? 'active' : 'inactive' }}">{{ $tag->is_active ? 'Activo' : 'Pausado' }}</span>
                </div>

                @unless ($tag->is_active)
                    <div class="alert alert-light border">Este Dog Tag no esta activo. Contacta al responsable o al taller.</div>
                @endunless

                <div class="scan-alert-box pet" data-scan-status>
                    <i class="bi bi-crosshair"></i>
                    <span>Comparte la ubicacion GPS para avisar a sus contactos.</span>
                    <button class="scan-location-button" type="button" data-scan-trigger>Enviar ubicacion</button>
                </div>

                <div class="pet-profile-band">
                    <div>
                        <span>Especie</span>
                        <strong>{{ $tag->pet_species ?: 'Mascota' }}</strong>
                    </div>
                    <div>
                        <span>Raza</span>
                        <strong>{{ $tag->pet_breed ?: 'No capturada' }}</strong>
                    </div>
                    <div>
                        <span>Tipo de sangre</span>
                        <strong>{{ $tag->blood_type ?: 'N/D' }}</strong>
                    </div>
                </div>

                <div class="qr-glass-panel pet-care">
                    <h2><i class="bi bi-bandaid-fill"></i> Cuidados importantes</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="qr-note">
                                <span>Alergias</span>
                                <strong>{{ $tag->allergies ?: 'Sin alergias registradas' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="qr-note">
                                <span>Notas de cuidado</span>
                                <strong>{{ $tag->medical_notes ?: 'Sin notas capturadas' }}</strong>
                            </div>
                        </div>
                        @if ($tag->public_notes)
                            <div class="col-12">
                                <div class="qr-note soft">
                                    <span>Mensaje del responsable</span>
                                    <strong>{{ $tag->public_notes }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="qr-contact-strip pet">
                    @if ($tag->owner_phone)
                        <a href="tel:{{ $tag->owner_phone }}"><i class="bi bi-telephone-fill"></i><span>Responsable<br><strong>{{ $tag->owner_name ?: 'Llamar' }}</strong><small>{{ $tag->owner_phone }}</small></span></a>
                        <a class="whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', $tag->owner_phone) }}?text=Hola%2C%20encontre%20a%20{{ urlencode($tag->display_name) }}%20y%20escanee%20su%20Dog%20Tag" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i><span>WhatsApp<br><strong>Avisar ubicacion</strong></span></a>
                    @endif
                    @if ($tag->vet_phone)
                        <a href="tel:{{ $tag->vet_phone }}"><i class="bi bi-heart-pulse-fill"></i><span>Veterinaria<br><strong>{{ $tag->vet_name ?: 'Llamar' }}</strong><small>{{ $tag->vet_phone }}</small></span></a>
                    @endif
                </div>

                <p class="qr-disclaimer">Este perfil ayuda a contactar al responsable. No sustituye atencion veterinaria ni servicios de emergencia.</p>
            </div>
        </div>
    </section>

    @include('tags.partials.scan-script', ['tag' => $tag])
@endsection
