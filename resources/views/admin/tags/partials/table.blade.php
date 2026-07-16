<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tags as $tag)
                <tr>
                    <td>
                        <span class="badge {{ $tag->type === 'biker' ? 'text-bg-warning' : 'text-bg-success' }}">{{ $tag->type_label }}</span>
                        @if ($tag->tag_code)
                            <div class="small text-secondary">{{ $tag->tag_code }}</div>
                        @endif
                        @if ($tag->type === 'biker' && $tag->is_blood_donor)
                            <div class="small text-danger fw-semibold">Donador</div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $tag->display_name }}</td>
                    <td>
                        <div>{{ $tag->owner_name ?: 'Sin nombre' }}</div>
                        <div class="small text-secondary">{{ $tag->owner_phone ?: 'Sin telefono' }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $tag->is_active ? 'text-bg-primary' : 'text-bg-secondary' }}">
                            {{ $tag->is_active ? 'Activo' : 'Pausado' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-dark" href="{{ route('admin.tags.show', $tag) }}" title="Ver"><i class="bi bi-eye"></i></a>
                            <a class="btn btn-outline-dark" href="{{ route('admin.tags.edit', $tag) }}" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a class="btn btn-outline-dark" href="{{ $tag->public_url }}" target="_blank" rel="noopener" title="Perfil publico"><i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center text-secondary py-4" colspan="5">Todavia no hay tags registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
