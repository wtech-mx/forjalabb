@extends('layouts.app')
@section('title', 'Inventario | ForjaLab')
@section('content')
<section class="admin-section"><div class="container">
    <div class="admin-header">
        <div><div class="eyebrow">Control de existencias</div><h1 class="fw-bold mt-2 mb-0"><i class="bi bi-boxes me-2 text-warning"></i>Inventario</h1><p class="text-secondary mt-2 mb-0">Las existencias se reservan únicamente mientras el pedido está en producción.</p></div>
        <a class="btn btn-outline-dark" href="{{ route('admin.catalog.index') }}"><i class="bi bi-bag-heart-fill me-2"></i>Ver catálogo</a>
    </div>
    <div class="inventory-stats">
        <article><i class="bi bi-box-seam"></i><span>Productos</span><strong>{{ $stats['products'] }}</strong></article>
        <article><i class="bi bi-stack"></i><span>Piezas disponibles</span><strong>{{ number_format($stats['units']) }}</strong></article>
        <article class="is-low"><i class="bi bi-exclamation-triangle-fill"></i><span>Stock bajo</span><strong>{{ $stats['low'] }}</strong></article>
        <article class="is-out"><i class="bi bi-x-octagon-fill"></i><span>Agotados</span><strong>{{ $stats['out'] }}</strong></article>
    </div>
    <form class="inventory-filters" method="GET">
        <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Buscar producto…"></div>
        <select class="form-select" name="status" onchange="this.form.submit()"><option value="">Todos los niveles</option><option value="low" @selected(request('status')==='low')>Stock bajo</option><option value="out" @selected(request('status')==='out')>Agotados</option></select>
        <button class="btn btn-dark">Filtrar</button>
    </form>
    <div class="row g-4"><div class="col-xl-8"><div class="inventory-grid">
        @forelse($products as $product)
            @php $level=$product->stock<=0?'out':($product->stock<=$product->minimum_stock?'low':'ok'); @endphp
            <article class="inventory-product" data-inventory-product data-product-id="{{ $product->id }}">
                <div class="inventory-product-image">@if($product->image_url)<img src="{{ $product->image_url }}" alt="">@else<i class="bi bi-box-seam"></i>@endif<span class="inventory-level is-{{ $level }}">{{ $product->stock<0?'Faltan '.abs($product->stock):($level==='out'?'Agotado':($level==='low'?'Stock bajo':'Disponible')) }}</span></div>
                <div class="inventory-product-body">
                    <h2>{{ $product->name }}</h2><div class="inventory-count"><strong data-stock>{{ $product->stock }}</strong><span>{{ $product->stock<0?'por surtir':'piezas' }}</span></div>
                    <small>Mínimo: <b data-minimum>{{ $product->minimum_stock }}</b> · {{ $product->variants_count }} variante{{ $product->variants_count===1?'':'s' }}</small>
                    @if($product->out_variants_count)<div class="mt-2"><span class="badge text-bg-danger"><i class="bi bi-x-octagon-fill me-1"></i>{{ $product->out_variants_count }} agotadas</span></div>@elseif($product->low_variants_count)<div class="mt-2"><span class="badge text-bg-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $product->low_variants_count }} bajas</span></div>@endif
                    <a class="btn btn-sm btn-outline-primary w-100 mt-3" href="{{ route('admin.inventory.variants',$product) }}"><i class="bi bi-grid-3x3-gap-fill me-1"></i>Matriz de variantes</a>
                    @can('inventory.manage')
                        <button class="btn btn-sm btn-outline-dark w-100 mt-2" type="button" data-adjust-button data-name="{{ $product->name }}" data-stock="{{ $product->stock }}" data-minimum="{{ $product->minimum_stock }}" data-variants="{{ base64_encode(json_encode($product->variants->map(fn($variant)=>['id'=>$variant->id,'label'=>$variant->label,'stock'=>$variant->stock,'minimum_stock'=>$variant->minimum_stock])->values())) }}" data-url="{{ route('admin.inventory.adjust',$product) }}"><i class="bi bi-arrow-left-right me-1"></i>Registrar movimiento</button>
                    @endcan
                </div>
            </article>
        @empty <div class="panel-card text-center text-secondary">No encontramos productos.</div> @endforelse
    </div><div class="mt-4">{{ $products->links() }}</div></div>
    <aside class="col-xl-4"><div class="panel-card inventory-forecast">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2"><div class="d-flex align-items-center gap-2"><i class="bi bi-clipboard2-check-fill"></i><h2 class="h5 fw-bold mb-0">Necesidades de pedidos</h2></div><a class="btn btn-sm btn-dark" href="{{ route('admin.inventory.purchase-list.pdf') }}"><i class="bi bi-file-earmark-pdf-fill me-1"></i>Descargar PDF</a></div>
        <p class="text-secondary small">Proyección de pedidos pendientes. Estas piezas todavía no se descuentan del inventario.</p>
        <div class="inventory-forecast-summary">
            <span><small>Pedidos</small><strong>{{ $pendingOrdersCount }}</strong></span>
            <span><small>Piezas apartadas</small><strong>{{ $pendingUnits }}</strong></span>
            <span class="{{ $purchaseUnits > 0 ? 'is-danger' : 'is-ok' }}"><small>Por comprar</small><strong>{{ $purchaseUnits }}</strong></span>
        </div>
        <div class="inventory-forecast-list">
            @forelse($pendingNeeds as $need)
                <article class="inventory-need {{ $need['needs_assignment'] ? 'needs-review' : ($need['shortage'] > 0 ? 'needs-purchase' : 'has-stock') }}">
                    <div class="inventory-need-heading">
                        <div><strong>{{ $need['product'] }}</strong><small>{{ $need['variant'] }}</small></div>
                        @if($need['needs_assignment'])
                            <span class="badge text-bg-warning">Revisar</span>
                        @elseif($need['shortage'] > 0)
                            <span class="badge text-bg-danger">Comprar {{ $need['shortage'] }}</span>
                        @else
                            <span class="badge text-bg-success">Sobran {{ $need['remaining'] }}</span>
                        @endif
                    </div>
                    @if($need['needs_assignment'])
                        <p>Hay {{ $need['required'] }} pieza{{ $need['required'] === 1 ? '' : 's' }} sin color o variante. Asígnala en el pedido para calcular la compra.</p>
                    @else
                        <div class="inventory-need-numbers">
                            <span><small>Había</small><b>{{ $need['available_before'] }}</b></span>
                            <span><small>Pendientes</small><b>-{{ $need['required'] }}</b></span>
                            <span><small>Resultado</small><b>{{ $need['remaining'] }}</b></span>
                        </div>
                    @endif
                    <small class="inventory-need-orders"><i class="bi bi-receipt me-1"></i>{{ $need['orders']->join(', ') }}</small>
                </article>
            @empty
                <div class="inventory-forecast-empty"><i class="bi bi-check-circle-fill"></i><strong>Sin pedidos pendientes</strong><span>No hay piezas por apartar o comprar.</span></div>
            @endforelse
        </div>
    </div></aside></div>
</div></section>

<style>
.inventory-forecast{position:sticky;top:1rem}.inventory-forecast>div:first-child>i{color:#ed741d;font-size:1.35rem}.inventory-forecast-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.55rem;margin:1rem 0}.inventory-forecast-summary span{padding:.7rem .45rem;text-align:center;background:#fff8ed;border:1px solid var(--line);border-radius:.65rem}.inventory-forecast-summary small,.inventory-forecast-summary strong{display:block}.inventory-forecast-summary small{color:var(--muted);font-size:.7rem}.inventory-forecast-summary strong{font-size:1.25rem}.inventory-forecast-summary .is-danger{color:#b42318;background:#fff2f0}.inventory-forecast-summary .is-ok{color:#21713d;background:#eef9f1}.inventory-forecast-list{display:grid;gap:.75rem;max-height:70vh;overflow:auto;padding-right:.15rem}.inventory-need{padding:.85rem;border:1px solid var(--line);border-left:4px solid #4d8a58;border-radius:.7rem;background:#fff}.inventory-need.needs-purchase{border-left-color:#dc3545;background:#fffafa}.inventory-need.needs-review{border-left-color:#e0a000;background:#fffaf0}.inventory-need-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:.6rem}.inventory-need-heading strong,.inventory-need-heading small{display:block}.inventory-need-heading small{color:var(--muted)}.inventory-need p{margin:.7rem 0;color:#725b48;font-size:.82rem}.inventory-need-numbers{display:grid;grid-template-columns:repeat(3,1fr);gap:.35rem;margin:.7rem 0}.inventory-need-numbers span{padding:.4rem;text-align:center;background:#f8f5f0;border-radius:.45rem}.inventory-need-numbers small,.inventory-need-numbers b{display:block}.inventory-need-numbers small{font-size:.65rem;color:var(--muted)}.inventory-need-orders{display:block;color:var(--muted);overflow-wrap:anywhere}.inventory-forecast-empty{display:grid;justify-items:center;gap:.25rem;padding:2rem 1rem;text-align:center;color:#347044;background:#f1faf3;border-radius:.75rem}.inventory-forecast-empty i{font-size:2rem}.inventory-forecast-empty span{font-size:.82rem;color:var(--muted)}@media(max-width:1199.98px){.inventory-forecast{position:static}.inventory-forecast-list{max-height:none}}
</style>

@can('inventory.manage')
<div class="modal fade" id="inventoryAdjustModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><div><div class="eyebrow">Entrada o salida</div><h2 class="modal-title h4 fw-bold" data-adjust-name></h2></div><button class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
    <form data-adjust-form><div class="modal-body">
        <label class="form-label">¿Dónde se aplicará?</label>
        <div class="input-group mb-3"><span class="input-group-text"><i class="bi bi-grid-3x3-gap-fill"></i></span><select class="form-select" name="variant_id" data-adjust-variant><option value="">Inventario general del producto</option></select></div>
        <div class="inventory-current"><span>Existencia seleccionada</span><strong data-adjust-current></strong></div>
        <label class="form-label mt-3">Cantidad del movimiento</label>
        <div class="input-group"><button class="btn btn-outline-danger" type="button" data-sign="out"><i class="bi bi-dash-lg"></i> Salida</button><input class="form-control text-center" type="number" name="quantity" min="1" placeholder="0" required><button class="btn btn-outline-success active" type="button" data-sign="in"><i class="bi bi-plus-lg"></i> Entrada</button></div>
        <small class="text-secondary d-block mt-1">Elige entrada para compras o salida para mermas y ajustes.</small>
        <label class="form-label mt-3">Motivo</label><input class="form-control" name="note" maxlength="255" required placeholder="Ej. Compra de proveedor, pieza dañada…">
        <label class="form-label mt-3">Avisar cuando queden</label><div class="input-group"><input class="form-control" type="number" name="minimum_stock" min="0"><span class="input-group-text">piezas</span></div>
        <div class="alert d-none mt-3 mb-0" data-adjust-message></div>
    </div><div class="modal-footer"><button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-dark" type="submit"><i class="bi bi-check2-circle me-2"></i>Guardar movimiento</button></div></form>
</div></div></div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
    const modalElement=document.querySelector('#inventoryAdjustModal'),modal=bootstrap.Modal.getOrCreateInstance(modalElement),form=modalElement.querySelector('[data-adjust-form]'),name=modalElement.querySelector('[data-adjust-name]'),current=modalElement.querySelector('[data-adjust-current]'),message=modalElement.querySelector('[data-adjust-message]'),quantity=form.elements.quantity,minimum=form.elements.minimum_stock,variantSelect=form.elements.variant_id;
    let url='',sign=1,productStock=0,productMinimum=0,variants=[];
    const selectedVariant=()=>variants.find(item=>String(item.id)===variantSelect.value);
    const syncSelection=()=>{const variant=selectedVariant();current.textContent=`${variant?variant.stock:productStock} piezas`;minimum.value=variant?variant.minimum_stock:productMinimum};
    document.querySelectorAll('[data-adjust-button]').forEach(button=>button.addEventListener('click',()=>{
        url=button.dataset.url;name.textContent=button.dataset.name;productStock=Number(button.dataset.stock);productMinimum=Number(button.dataset.minimum);variants=JSON.parse(atob(button.dataset.variants||'W10='));
        variantSelect.innerHTML='<option value="">Inventario general del producto</option>'+variants.map(item=>`<option value="${item.id}">${item.label} · ${item.stock} piezas</option>`).join('');
        variantSelect.disabled=!variants.length;quantity.value='';form.elements.note.value='';message.className='alert d-none mt-3 mb-0';sign=1;modalElement.querySelectorAll('[data-sign]').forEach(item=>item.classList.toggle('active',item.dataset.sign==='in'));syncSelection();modal.show();
    }));
    variantSelect.addEventListener('change',syncSelection);
    modalElement.querySelectorAll('[data-sign]').forEach(button=>button.addEventListener('click',()=>{sign=button.dataset.sign==='in'?1:-1;modalElement.querySelectorAll('[data-sign]').forEach(item=>item.classList.toggle('active',item===button))}));
    form.addEventListener('submit',async event=>{event.preventDefault();const submit=form.querySelector('[type=submit]');submit.disabled=true;try{const response=await fetch(url,{method:'POST',headers:{Accept:'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({variant_id:variantSelect.value||null,quantity:sign*Math.abs(Number(quantity.value)),note:form.elements.note.value,minimum_stock:Number(minimum.value)})}),payload=await response.json();if(!response.ok)throw new Error(payload.message||Object.values(payload.errors||{}).flat()[0]);await Swal.fire({title:'Inventario actualizado',text:payload.message,icon:'success',confirmButtonText:'Listo'});location.reload()}catch(error){message.className='alert alert-danger mt-3 mb-0';message.textContent=error.message}finally{submit.disabled=false}});
});
</script>
@endcan
@endsection
