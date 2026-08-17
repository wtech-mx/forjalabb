@extends('layouts.app')
@section('title','Preparar envío | '.$order->folio)
@section('content')
<section class="admin-section"><div class="container"><div class="admin-header"><div><div class="eyebrow">Pedido {{ $order->folio }}</div><h1>Preparar envío</h1><p class="text-secondary mb-0">{{ $order->customer->name }}</p></div></div>
<form class="panel-card" id="shipment-create-form" method="POST" action="{{ route('admin.shipments.store',$order) }}">@csrf
@include('admin.shipments.partials.fields')
<div id="shipping-api-message" class="alert d-none mt-4"></div><div id="shipping-rates" class="shipping-rates mt-4"></div>
<input type="hidden" name="rate_id" id="selected-rate-id"><input type="hidden" name="rate_carrier" id="selected-rate-carrier"><input type="hidden" name="rate_service" id="selected-rate-service"><input type="hidden" name="rate_price" id="selected-rate-price">
<div class="mt-4 d-flex gap-2 flex-wrap"><button class="btn btn-primary" id="quote-shipment" type="button"><i class="bi bi-calculator me-2"></i>Cotizar con Skydropx</button><button class="btn btn-dark"><i class="bi bi-truck me-2"></i><span id="shipment-submit-label">Crear seguimiento</span></button><a class="btn btn-outline-dark" href="{{ route('admin.orders.show',$order) }}">Cancelar</a></div>
</form></div></section>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{const form=document.querySelector('#shipment-create-form'),cp=document.querySelector('#shipping-postal-code'),msg=document.querySelector('#shipping-api-message'),rates=document.querySelector('#shipping-rates');
const message=(text,type='danger')=>{msg.className=`alert alert-${type} mt-4`;msg.textContent=text};
document.querySelector('#lookup-postal-code').addEventListener('click',async()=>{if(!/^\d{5}$/.test(cp.value))return message('Escribe un código postal de 5 dígitos.');message('Consultando dirección…','info');try{const r=await fetch(`{{ url('/admin/shipments/postal-code') }}/${cp.value}`,{headers:{Accept:'application/json'}}),j=await r.json();if(!r.ok)throw new Error(j.message);const places=j.places||[];if(!places.length)throw new Error('No encontramos colonias para ese CP.');document.querySelector('#shipping-state').value=places[0].state;document.querySelector('#shipping-city').value=places[0].city;document.querySelector('#shipping-neighborhood').innerHTML=places.map(p=>`<option>${p.neighborhood}</option>`).join('');message(`${places.length} colonia(s) encontradas.`,'success')}catch(e){message(e.message)}});
document.querySelector('#quote-shipment').addEventListener('click',async()=>{message('Consultando tarifas de Skydropx…','info');rates.innerHTML='';const data=new FormData(form);try{const r=await fetch(`{{ route('admin.shipments.draft-quote',$order) }}`,{method:'POST',body:data,headers:{Accept:'application/json'}}),j=await r.json();if(!r.ok)throw new Error(j.message||Object.values(j.errors||{})[0]?.[0]);if(!j.rates?.length)throw new Error('La cotización aún no tiene tarifas. Intenta nuevamente en unos segundos.');rates.innerHTML='<h2 class="h5 fw-bold">Elige una tarifa</h2>'+j.rates.map((x,i)=>`<label class="shipping-rate"><input type="radio" name="rate_option" data-rate='${JSON.stringify(x).replaceAll("'","&#39;")}' ${i===0?'checked':''}><span><strong>${x.carrier} · ${x.service}</strong><small>${x.days?x.days+' días estimados':'Tiempo por confirmar'}</small></span><b>$${Number(x.price).toFixed(2)}</b></label>`).join('');selectRate(rates.querySelector('input:checked'));message('Tarifas listas. Elige una y genera tu guía.','success')}catch(e){message(e.message)}});
const selectRate=input=>{if(!input)return;const x=JSON.parse(input.dataset.rate);document.querySelector('#selected-rate-id').value=x.id;document.querySelector('#selected-rate-carrier').value=x.carrier;document.querySelector('#selected-rate-service').value=x.service;document.querySelector('#selected-rate-price').value=x.price;document.querySelector('#shipment-submit-label').textContent='Crear envío y generar guía'};rates.addEventListener('change',e=>{if(e.target.matches('[name=rate_option]'))selectRate(e.target)});
});
</script>
@endpush
