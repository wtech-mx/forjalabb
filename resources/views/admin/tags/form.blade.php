@extends('layouts.app')

@php
    $isEdit = $tag->exists;
    $selectedType = old('type', $type);
    $isBiker = $selectedType === 'biker';
    $bloodTypes = \App\Models\SmartTag::bloodTypes();
@endphp

@section('title', ($isEdit ? 'Editar tag' : 'Nuevo tag').' | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">{{ $isEdit ? 'Editar' : 'Nuevo' }}</div>
                    <h1 class="fw-bold mt-2 mb-0">{{ $isBiker ? 'Biker Tag QR' : 'Dog Tag QR' }}</h1>
                </div>
                <a class="btn btn-outline-dark" href="{{ route('admin.tags.index') }}"><i class="bi bi-arrow-left me-2"></i>Volver</a>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.tags.update', $tag) : route('admin.tags.store') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-upc-scan"></i>
                                <div>
                                    <h2>Identidad del tag</h2>
                                    <p>Define el giro del producto y los datos que forman el folio automatico.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="type">Tipo de producto</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-tags-fill"></i></span>
                                        <select class="form-select" id="type" name="type" data-product-select>
                                            <option value="biker" @selected($selectedType === 'biker')>Biker Tag</option>
                                            <option value="dog" @selected($selectedType === 'dog')>Dog Tag</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="tag_code">Codigo interno</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-fingerprint"></i></span>
                                        <input class="form-control" id="tag_code" value="{{ $tag->tag_code ?: 'Se genera al guardar' }}" readonly>
                                    </div>
                                    <div class="form-text">Producto + sangre + donador + secuencia unica.</div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="display_name" data-biker-label="Nombre del biker" data-dog-label="Nombre de la mascota">{{ $isBiker ? 'Nombre del biker' : 'Nombre de la mascota' }}</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-person-badge-fill" data-product-icon></i></span>
                                        <input class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $tag->display_name) }}" required>
                                        @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="blood_type">Tipo de sangre</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-droplet-half"></i></span>
                                        <select class="form-select @error('blood_type') is-invalid @enderror" id="blood_type" name="blood_type">
                                            <option value="">Sin capturar</option>
                                            @foreach ($bloodTypes as $bloodType)
                                                <option value="{{ $bloodType }}" @selected(old('blood_type', $tag->blood_type) === $bloodType)>{{ $bloodType }}</option>
                                            @endforeach
                                        </select>
                                        @error('blood_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6 product-only" data-product-section="biker">
                                    <label class="form-label d-block">Donador de sangre</label>
                                    <div class="switch-card">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" id="is_blood_donor" name="is_blood_donor" type="checkbox" value="1" @checked(old('is_blood_donor', $tag->is_blood_donor))>
                                            <label class="form-check-label" for="is_blood_donor">Si, es donador</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-telephone-inbound-fill"></i>
                                <div>
                                    <h2>Contactos de emergencia</h2>
                                    <p>Estos correos reciben la alerta cuando se escanea el QR y se autoriza ubicacion.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="owner_name" data-biker-label="Contacto principal" data-dog-label="Responsable">{{ $isBiker ? 'Contacto principal' : 'Responsable' }}</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input class="form-control" id="owner_name" name="owner_name" value="{{ old('owner_name', $tag->owner_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="owner_phone">Telefono principal</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                                        <input class="form-control" id="owner_phone" name="owner_phone" value="{{ old('owner_phone', $tag->owner_phone) }}" placeholder="+52 55...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="owner_email">Correo principal</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-envelope-at-fill"></i></span>
                                        <input class="form-control @error('owner_email') is-invalid @enderror" id="owner_email" name="owner_email" type="email" value="{{ old('owner_email', $tag->owner_email) }}" placeholder="contacto@email.com">
                                        @error('owner_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="secondary_contact_name">Contacto secundario</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-person-plus-fill"></i></span>
                                        <input class="form-control" id="secondary_contact_name" name="secondary_contact_name" value="{{ old('secondary_contact_name', $tag->secondary_contact_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="secondary_contact_phone">Telefono secundario</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-telephone-plus-fill"></i></span>
                                        <input class="form-control" id="secondary_contact_phone" name="secondary_contact_phone" value="{{ old('secondary_contact_phone', $tag->secondary_contact_phone) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="secondary_contact_email">Correo secundario</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-envelope-plus-fill"></i></span>
                                        <input class="form-control @error('secondary_contact_email') is-invalid @enderror" id="secondary_contact_email" name="secondary_contact_email" type="email" value="{{ old('secondary_contact_email', $tag->secondary_contact_email) }}">
                                        @error('secondary_contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-card mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-shield-fill-plus"></i>
                                <div>
                                    <h2>Datos de emergencia</h2>
                                    <p>Informacion corta, clara y util para quien escanea el QR.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="allergies">Alergias</label>
                                    <textarea class="form-control" id="allergies" name="allergies" rows="3">{{ old('allergies', $tag->allergies) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="medical_notes">Notas medicas o cuidados</label>
                                    <textarea class="form-control" id="medical_notes" name="medical_notes" rows="3">{{ old('medical_notes', $tag->medical_notes) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="public_notes">Notas visibles en el perfil</label>
                                    <textarea class="form-control" id="public_notes" name="public_notes" rows="3">{{ old('public_notes', $tag->public_notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="panel-card mb-4 product-only" data-product-section="biker">
                            <div class="form-section-title biker">
                                <i class="bi bi-bicycle"></i>
                                <div>
                                    <h2>Datos Biker Tag</h2>
                                    <p>Moto, placas y club para identificar mejor al rider.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="vehicle">Moto / vehiculo</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-speedometer2"></i></span>
                                        <input class="form-control" id="vehicle" name="vehicle" value="{{ old('vehicle', $tag->vehicle) }}" placeholder="Yamaha MT-07">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="motorcycle_plate">Placas de moto</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-credit-card-2-front-fill"></i></span>
                                        <input class="form-control" id="motorcycle_plate" name="motorcycle_plate" value="{{ old('motorcycle_plate', $tag->motorcycle_plate) }}" placeholder="ABC123">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="club_name">Club</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
                                        <input class="form-control" id="club_name" name="club_name" value="{{ old('club_name', $tag->club_name) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-card product-only" data-product-section="dog">
                            <div class="form-section-title dog">
                                <i class="bi bi-heart-fill"></i>
                                <div>
                                    <h2>Datos Dog Tag</h2>
                                    <p>Informacion de mascota, veterinaria y cuidados de contacto.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="pet_species">Especie</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-stars"></i></span>
                                        <input class="form-control" id="pet_species" name="pet_species" value="{{ old('pet_species', $tag->pet_species) }}" placeholder="Perro / gato">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="pet_breed">Raza</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-award-fill"></i></span>
                                        <input class="form-control" id="pet_breed" name="pet_breed" value="{{ old('pet_breed', $tag->pet_breed) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="vet_name">Veterinaria</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-hospital-fill"></i></span>
                                        <input class="form-control" id="vet_name" name="vet_name" value="{{ old('vet_name', $tag->vet_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="vet_phone">Telefono veterinaria</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                        <input class="form-control" id="vet_phone" name="vet_phone" value="{{ old('vet_phone', $tag->vet_phone) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="vet_email">Correo veterinaria</label>
                                    <div class="input-group icon-input">
                                        <span class="input-group-text"><i class="bi bi-envelope-heart-fill"></i></span>
                                        <input class="form-control @error('vet_email') is-invalid @enderror" id="vet_email" name="vet_email" type="email" value="{{ old('vet_email', $tag->vet_email) }}">
                                        @error('vet_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="panel-card sticky-copy">
                            <div class="form-section-title compact">
                                <i class="bi bi-broadcast-pin"></i>
                                <div>
                                    <h2>Publicacion</h2>
                                    <p>Controla si el QR responde publicamente.</p>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $tag->is_active))>
                                <label class="form-check-label" for="is_active">Perfil activo</label>
                            </div>
                            <label class="form-label" for="expires_at">Vence</label>
                            <input class="form-control mb-4" id="expires_at" name="expires_at" type="date" value="{{ old('expires_at', optional($tag->expires_at)->format('Y-m-d')) }}">
                            <button class="btn btn-dark w-100" type="submit">
                                <i class="bi bi-save me-2"></i>{{ $isEdit ? 'Guardar cambios' : 'Crear y generar QR' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        (() => {
            const select = document.querySelector('[data-product-select]');
            const sections = document.querySelectorAll('[data-product-section]');
            const labelTargets = document.querySelectorAll('[data-biker-label][data-dog-label]');
            const icon = document.querySelector('[data-product-icon]');

            const updateProductFields = () => {
                const product = select?.value || 'biker';
                sections.forEach((section) => {
                    section.hidden = section.dataset.productSection !== product;
                });
                labelTargets.forEach((label) => {
                    label.textContent = product === 'biker' ? label.dataset.bikerLabel : label.dataset.dogLabel;
                });
                if (icon) {
                    icon.className = product === 'biker' ? 'bi bi-person-badge-fill' : 'bi bi-heart-fill';
                }
            };

            select?.addEventListener('change', updateProductFields);
            updateProductFields();
        })();
    </script>
@endsection
