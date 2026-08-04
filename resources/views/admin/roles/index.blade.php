@extends('layouts.app')

@section('title', 'Roles | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Permisos</div>
                    <h1 class="fw-bold mt-2 mb-0">Roles</h1>
                </div>
                @can('roles.manage')
                    <a class="btn btn-dark" href="{{ route('admin.roles.create') }}">
                        <i class="bi bi-shield-plus me-2"></i>Nuevo rol
                    </a>
                @endcan
            </div>

            <div class="panel-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Rol</th>
                                <th>Permisos</th>
                                <th>Usuarios</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $role->name }}</div>
                                        <div class="small text-secondary">{{ $role->description ?: $role->slug }}</div>
                                    </td>
                                    <td><span class="badge text-bg-light">{{ $role->permissions_count }} permisos</span></td>
                                    <td><span class="badge text-bg-light">{{ $role->users_count }} usuarios</span></td>
                                    <td class="text-end">
                                        @can('roles.manage')
                                            <div class="btn-group btn-group-sm">
                                                <a class="btn btn-outline-dark" href="{{ route('admin.roles.edit', $role) }}" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('¿Eliminar este rol?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger" type="submit" title="Eliminar" @disabled($role->isSuperAdmin() || $role->users_count > 0)>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center text-secondary py-4" colspan="4">Todavia no hay roles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $roles->links() }}</div>
            </div>
        </div>
    </section>
@endsection
