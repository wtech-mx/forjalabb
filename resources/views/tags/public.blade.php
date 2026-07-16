@extends('layouts.app')

@section('title', 'Perfil de emergencia | LabCustom')

@section('content')
    <section class="public-profile-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="emergency-card">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                            <div>
                                <span class="badge {{ $tag->type === 'biker' ? 'text-bg-warning' : 'text-bg-success' }}">{{ $tag->type_label }}</span>
                                <h1 class="fw-bold mt-3 mb-1">{{ $tag->display_name }}</h1>
                                <p class="text-secondary mb-0">Perfil de emergencia conectado por QR.</p>
                            </div>
                            <span class="status-pill {{ $tag->is_active ? 'active' : 'inactive' }}">{{ $tag->is_active ? 'Activo' : 'Pausado' }}</span>
                        </div>

                        @unless ($tag->is_active)
                            <div class="alert alert-secondary">Este perfil no esta activo. Contacta al propietario o al taller.</div>
                        @endunless

                        <div class="row g-3 mb-4">
                            @foreach ([
                                'Tipo de sangre' => $tag->blood_type,
                                'Alergias' => $tag->allergies,
                                'Notas medicas' => $tag->medical_notes,
                                'Moto / vehiculo' => $tag->vehicle,
                                'Club' => $tag->club_name,
                                'Especie' => $tag->pet_species,
                                'Raza' => $tag->pet_breed,
                                'Notas' => $tag->public_notes,
                            ] as $label => $value)
                                @if ($value)
                                    <div class="col-md-6">
                                        <div class="public-info">
                                            <span>{{ $label }}</span>
                                            <strong>{{ $value }}</strong>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="contact-panel">
                            <h2 class="h5 fw-bold mb-3">Contactos</h2>
                            <div class="row g-3">
                                @if ($tag->owner_phone)
                                    <div class="col-md-6">
                                        <a class="contact-button" href="tel:{{ $tag->owner_phone }}">
                                            <i class="bi bi-telephone-fill"></i>
                                            <span>
                                                <small>Principal</small>
                                                {{ $tag->owner_name ?: $tag->owner_phone }}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="contact-button whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', $tag->owner_phone) }}?text=Hola%2C%20escanee%20el%20QR%20de%20{{ urlencode($tag->display_name) }}" target="_blank" rel="noopener">
                                            <i class="bi bi-whatsapp"></i>
                                            <span>
                                                <small>WhatsApp</small>
                                                Avisar al responsable
                                            </span>
                                        </a>
                                    </div>
                                @endif

                                @if ($tag->secondary_contact_phone)
                                    <div class="col-md-6">
                                        <a class="contact-button" href="tel:{{ $tag->secondary_contact_phone }}">
                                            <i class="bi bi-telephone-plus-fill"></i>
                                            <span>
                                                <small>Secundario</small>
                                                {{ $tag->secondary_contact_name ?: $tag->secondary_contact_phone }}
                                            </span>
                                        </a>
                                    </div>
                                @endif

                                @if ($tag->vet_phone)
                                    <div class="col-md-6">
                                        <a class="contact-button" href="tel:{{ $tag->vet_phone }}">
                                            <i class="bi bi-heart-pulse-fill"></i>
                                            <span>
                                                <small>Veterinaria</small>
                                                {{ $tag->vet_name ?: $tag->vet_phone }}
                                            </span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="small text-secondary mt-4 mb-0">Este perfil ayuda a contactar al responsable. No sustituye servicios medicos, veterinarios ni de emergencia.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
