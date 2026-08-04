@extends('layouts.app')

@section('title', 'Usuarios | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Accesos</div>
                    <h1 class="fw-bold mt-2 mb-0">Usuarios</h1>
                </div>
                @can('users.manage')
                    <a class="btn btn-dark" href="{{ route('admin.users.create') }}">
                        <i class="bi bi-person-plus-fill me-2"></i>Nuevo usuario
                    </a>
                @endcan
            </div>

            <div class="panel-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Roles</th>
                                <th>Alta</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="small text-secondary">{{ $user->email }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse ($user->roles as $role)
                                                <span class="badge {{ $role->isSuperAdmin() ? 'text-bg-dark' : 'text-bg-light' }}">{{ $role->name }}</span>
                                            @empty
                                                <span class="text-secondary small">Sin rol</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="text-secondary">{{ $user->created_at?->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        @can('users.manage')
                                            <div class="btn-group btn-group-sm">
                                                <a class="btn btn-outline-dark" href="{{ route('admin.users.edit', $user) }}" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger" type="submit" title="Eliminar" @disabled(auth()->id() === $user->id)>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center text-secondary py-4" colspan="4">Todavia no hay usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $users->links() }}</div>
            </div>
        </div>
    </section>
@endsection
