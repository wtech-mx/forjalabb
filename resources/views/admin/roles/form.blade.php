@extends('layouts.app')

@php
    $isEdit = $role->exists;
    $isLocked = $role->exists && $role->isSuperAdmin();
@endphp

@section('title', ($isEdit ? 'Editar rol' : 'Nuevo rol').' | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">{{ $isEdit ? 'Editar' : 'Nuevo' }}</div>
                    <h1 class="fw-bold mt-2 mb-0">Rol y permisos</h1>
                </div>
                <a class="btn btn-outline-dark" href="{{ route('admin.roles.index') }}">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="panel-card sticky-copy">
                            <div class="form-section-title compact">
                                <i class="bi bi-shield-lock-fill"></i>
                                <div>
                                    <h2>Identidad del rol</h2>
                                    <p>Nombre interno para agrupar permisos.</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="name">Nombre</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="slug">Slug</label>
                                <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $role->slug) }}" @readonly($isLocked)>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">{{ $isLocked ? 'El slug del Super admin no se puede cambiar.' : 'Si lo dejas vacío se genera con el nombre.' }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="description">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $role->description) }}</textarea>
                            </div>
                            <button class="btn btn-dark w-100" type="submit">
                                <i class="bi bi-save me-2"></i>{{ $isEdit ? 'Guardar rol' : 'Crear rol' }}
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="panel-card">
                            <div class="form-section-title">
                                <i class="bi bi-ui-checks-grid"></i>
                                <div>
                                    <h2>Permisos</h2>
                                    <p>{{ $isLocked ? 'Super admin conserva acceso total.' : 'Selecciona lo que este rol puede consultar o modificar.' }}</p>
                                </div>
                            </div>
                            <div class="permission-grid">
                                @foreach ($permissions as $group => $items)
                                    <div class="permission-group">
                                        <h3>{{ $group }}</h3>
                                        @foreach ($items as $permission)
                                            <label class="permission-check">
                                                <input class="form-check-input" name="permissions[]" type="checkbox" value="{{ $permission->id }}" @checked($isLocked || in_array($permission->id, old('permissions', $selectedPermissions), true)) @disabled($isLocked)>
                                                <span>{{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
