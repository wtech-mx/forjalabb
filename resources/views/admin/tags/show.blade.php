@extends('layouts.app')

@section('title', $tag->display_name.' | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="admin-header">
                <div>
                    <div class="eyebrow">{{ $tag->type_label }}</div>
                    <h1 class="fw-bold mt-2 mb-0">{{ $tag->display_name }}</h1>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-dark" href="{{ route('admin.tags.edit', $tag) }}"><i class="bi bi-pencil me-2"></i>Editar</a>
                    <a class="btn btn-dark" href="{{ route('admin.tags.index') }}"><i class="bi bi-list-ul me-2"></i>Listado</a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="panel-card text-center">
                        @if($tag->is_active)
                            <img class="qr-preview" src="{{ route('admin.tags.qr', $tag) }}" alt="QR para {{ $tag->display_name }}">
                            <div class="small text-secondary mt-3 text-break">{{ $tag->public_url }}</div>
                            <div class="d-grid gap-2 mt-3">
                            <a class="btn btn-dark" href="{{ route('admin.tags.qr', $tag) }}" target="_blank" rel="noopener"><i class="bi bi-qr-code me-2"></i>Abrir QR SVG</a>
                            <a class="btn btn-outline-dark" href="{{ $tag->public_url }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-2"></i>Ver perfil publico</a>
                            </div>
                        @else
                            <div class="pending-tag-icon"><i class="bi bi-lock-fill"></i></div>
                            <h2 class="h4 fw-bold">{{ $tag->intake_status==='pending_payment'?'Pago pendiente':'Esperando datos' }}</h2>
                            <p class="text-secondary">El QR permanecerá bloqueado hasta que el cliente ingrese el código de pago.</p>
                            @if($tag->intake_url)
                                <label class="form-label">Enlace para el cliente</label>
                                <div class="input-group"><input class="form-control" value="{{ $tag->intake_url }}" readonly id="clientIntakeUrl"><button class="btn btn-dark" type="button" onclick="navigator.clipboard.writeText(document.getElementById('clientIntakeUrl').value);this.innerHTML='Copiado'"><i class="bi bi-copy"></i></button></div>
                                <a class="btn btn-outline-dark w-100 mt-2" href="{{ $tag->intake_url }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>Abrir formulario</a>
                                <div class="alert alert-warning mt-3 mb-0"><strong>Código de pago: {{ $tag->payment_code }}</strong><small class="d-block">Entrégalo únicamente cuando confirmes el pago.</small></div>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="panel-card">
                        <h2 class="h5 fw-bold mb-3">Datos guardados</h2>
                        <div class="detail-grid">
                            @foreach ([
                                'Estado' => $tag->is_active ? 'Activo' : 'Pausado',
                                'Codigo interno' => $tag->tag_code,
                                'Contacto principal' => $tag->owner_name,
                                'Telefono principal' => $tag->owner_phone,
                                'Correo principal' => $tag->owner_email,
                                'Contacto secundario' => $tag->secondary_contact_name,
                                'Telefono secundario' => $tag->secondary_contact_phone,
                                'Correo secundario' => $tag->secondary_contact_email,
                                'Tipo de sangre' => $tag->blood_type,
                                'Donador de sangre' => $tag->type === 'biker' ? ($tag->is_blood_donor ? 'Si' : 'No') : 'No aplica',
                                'Moto / vehiculo' => $tag->vehicle,
                                'Placas de moto' => $tag->motorcycle_plate,
                                'Club' => $tag->club_name,
                                'Especie' => $tag->pet_species,
                                'Raza' => $tag->pet_breed,
                                'Veterinaria' => $tag->vet_name,
                                'Telefono veterinaria' => $tag->vet_phone,
                                'Correo veterinaria' => $tag->vet_email,
                            ] as $label => $value)
                                <div>
                                    <span>{{ $label }}</span>
                                    <strong>{{ $value ?: 'No capturado' }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                        <h3 class="h6 fw-bold">Alergias</h3>
                        <p class="text-secondary">{{ $tag->allergies ?: 'No capturado' }}</p>
                        <h3 class="h6 fw-bold">Notas medicas</h3>
                        <p class="text-secondary">{{ $tag->medical_notes ?: 'No capturado' }}</p>
                        <h3 class="h6 fw-bold">Notas publicas</h3>
                        <p class="text-secondary mb-0">{{ $tag->public_notes ?: 'No capturado' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
