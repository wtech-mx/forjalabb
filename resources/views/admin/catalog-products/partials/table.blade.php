<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Orden</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $product->name }}</div>
                        <div class="small text-secondary">{{ $product->slug }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">${{ number_format((float) $product->public_price, 2) }}</div>
                        <div class="small text-secondary">Costo ${{ number_format((float) $product->cost_subtotal, 2) }}</div>
                    </td>
                    <td>
                        <span class="badge text-bg-light">{{ $product->stock }}</span>
                        @if ($product->is_featured)
                            <span class="badge text-bg-warning">Destacado</span>
                        @endif
                    </td>
                    <td>{{ $product->sort_order }}</td>
                    <td>
                        <span class="badge {{ $product->is_active ? 'text-bg-primary' : 'text-bg-secondary' }}">
                            {{ $product->is_active ? 'Activo' : 'Oculto' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-dark" href="{{ route('admin.catalog.preview', $product) }}" title="Vista previa"><i class="bi bi-eye"></i></a>
                            @can('catalog.manage')
                                <a class="btn btn-outline-dark" href="{{ route('admin.catalog.edit', $product) }}" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.catalog.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center text-secondary py-4" colspan="5">Todavia no hay productos en catalogo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
