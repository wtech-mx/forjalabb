@extends('layouts.app')

@section('title', 'Catálogo PDF y mayoreo | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div><div class="eyebrow">Precios personalizados</div><h1 class="fw-bold mt-2 mb-0">Catálogo PDF y mayoreo</h1></div>
                <a class="btn btn-outline-dark" href="{{ route('admin.catalog.index') }}">Ver catálogo</a>
            </div>
            <div class="panel-card">
                <p>Selecciona los productos, escribe el precio que ofrecerás y define desde cuántas piezas aplica el mayoreo. Los precios del catálogo no se modifican.</p>
                <div class="alert alert-light border">Los precios se indican por la presentación mostrada. En productos con paquetes se usa la presentación predeterminada de la página. El mínimo de mayoreo siempre se expresa en piezas.</div>
                <form method="POST" action="{{ route('admin.catalog-quotes.pdf') }}" id="catalog-quote-form">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><label class="form-label" for="recipient">Cliente o destinatario (opcional)</label><input class="form-control" id="recipient" name="recipient" maxlength="160" value="{{ old('recipient') }}"></div>
                        <div class="col-md-6"><label class="form-label" for="product-search">Buscar producto</label><input class="form-control" id="product-search" type="search" placeholder="Nombre del producto"></div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center mb-3">
                        <button type="button" class="btn btn-sm btn-outline-dark" id="select-visible">Seleccionar visibles</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-selection">Quitar selección</button>
                        <span class="text-muted" id="selection-count" aria-live="polite"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Incluir</th><th>Producto / presentación</th><th>Precio en página</th><th>Precio ofrecido (MXN)</th><th>Mayoreo desde (piezas)</th></tr></thead>
                            <tbody>
                                @forelse ($products as $product)
                                    @php
                                        $package = $product->salePackages->firstWhere('is_default', true) ?: $product->salePackages->first();
                                        $publicPrice = $package?->public_price ?? $product->public_price;
                                        $selected = old('items.'.$product->id.'.product_id') !== null;
                                    @endphp
                                    <tr data-product-row data-name="{{ $product->name }}">
                                        <td><input class="form-check-input" type="checkbox" name="items[{{ $product->id }}][product_id]" value="{{ $product->id }}" aria-label="Incluir {{ $product->name }}" @checked($selected) data-select></td>
                                        <td><strong>{{ $product->name }}</strong>@unless($product->is_active) <span class="badge text-bg-secondary">Inactivo</span> @endunless<br><small class="text-muted">{{ $package ? $package->name.' ('.$package->quantity.' piezas)' : '1 pieza' }}</small></td>
                                        <td class="text-nowrap">{{ $publicPrice > 0 ? '$'.number_format((float) $publicPrice, 2).' MXN' : 'Por cotizar' }}</td>
                                        <td><input style="min-width: 130px" class="form-control" type="number" name="items[{{ $product->id }}][price]" value="{{ old('items.'.$product->id.'.price', $publicPrice > 0 ? $publicPrice : '') }}" min="0" max="9999999.99" step="0.01" aria-label="Precio ofrecido de {{ $product->name }}" data-detail @disabled(!$selected) required></td>
                                        <td><input style="min-width: 110px" class="form-control" type="number" name="items[{{ $product->id }}][minimum_quantity]" value="{{ old('items.'.$product->id.'.minimum_quantity', 1) }}" min="1" max="1000000" step="1" aria-label="Mínimo de mayoreo de {{ $product->name }}" data-detail @disabled(!$selected) required></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">Todavía no hay productos en el catálogo.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted d-none" id="no-results">No hay productos que coincidan con la búsqueda.</p>
                    <div class="mt-3"><label class="form-label" for="quote-notes">Notas o condiciones (opcional)</label><textarea class="form-control" name="notes" id="quote-notes" rows="3" maxlength="2000" placeholder="Vigencia, tiempos de entrega o condiciones de pago">{{ old('notes') }}</textarea></div>
                    <div class="mt-4"><button class="btn btn-dark" type="submit" id="generate-pdf" disabled><i class="bi bi-file-earmark-pdf me-2"></i>Descargar PDF</button></div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('catalog-quote-form');
        const rows = [...form.querySelectorAll('[data-product-row]')];
        const update = () => {
            let count = 0;
            rows.forEach(row => {
                const selected = row.querySelector('[data-select]').checked;
                if (selected) count++;
                row.querySelectorAll('[data-detail]').forEach(input => input.disabled = !selected);
            });
            document.getElementById('selection-count').textContent = `${count} producto(s) seleccionado(s)`;
            document.getElementById('generate-pdf').disabled = count === 0;
        };
        const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es');
        document.getElementById('product-search').addEventListener('input', event => {
            const query = normalize(event.target.value.trim());
            rows.forEach(row => row.hidden = !normalize(row.dataset.name).includes(query));
            document.getElementById('no-results').classList.toggle('d-none', rows.some(row => !row.hidden));
        });
        form.addEventListener('change', update);
        document.getElementById('select-visible').addEventListener('click', () => {
            rows.filter(row => !row.hidden).forEach(row => row.querySelector('[data-select]').checked = true);
            update();
        });
        document.getElementById('clear-selection').addEventListener('click', () => {
            rows.forEach(row => row.querySelector('[data-select]').checked = false);
            update();
        });
        update();
    })();
</script>
@endpush
