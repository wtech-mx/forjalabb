@extends('layouts.app')

@php
    $isEdit = $user->exists;
@endphp

@section('title', ($isEdit ? 'Editar usuario' : 'Nuevo usuario').' | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">{{ $isEdit ? 'Editar' : 'Nuevo' }}</div>
                    <h1 class="fw-bold mt-2 mb-0">Usuario administrativo</h1>
                </div>
                <a class="btn btn-outline-dark" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="panel-card">
                            <div class="form-section-title">
                                <i class="bi bi-person-badge-fill"></i>
                                <div>
                                    <h2>Datos de acceso</h2>
                                    <p>Nombre, correo y contraseña para entrar al panel.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Nombre</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">Correo</label>
                                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="password">Contraseña</label>
                                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" {{ $isEdit ? '' : 'required' }}>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($isEdit)
                                        <div class="form-text">Déjala vacía para conservar la actual.</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" {{ $isEdit ? '' : 'required' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="panel-card sticky-copy">
                            <div class="form-section-title compact">
                                <i class="bi bi-shield-check"></i>
                                <div>
                                    <h2>Roles asignados</h2>
                                    <p>Un usuario debe tener al menos un rol.</p>
                                </div>
                            </div>
                            <div class="role-check-list">
                                @foreach ($roles as $role)
                                    <label class="check-item">
                                        <input class="form-check-input mt-1" name="roles[]" type="checkbox" value="{{ $role->id }}" @checked(in_array($role->id, old('roles', $selectedRoles), true))>
                                        <span>
                                            <strong>{{ $role->name }}</strong>
                                            <span>{{ $role->description ?: 'Sin descripcion.' }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            <button class="btn btn-dark w-100 mt-4" type="submit">
                                <i class="bi bi-save me-2"></i>{{ $isEdit ? 'Guardar usuario' : 'Crear usuario' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
