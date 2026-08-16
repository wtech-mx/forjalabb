@extends('layouts.app')

@section('title', 'Catálogo digital '.($showPrices ? 'con precios' : 'sin precios').' | ForjaLab')
@section('meta_description', 'Explora el catálogo digital de ForjaLab como una revista interactiva: productos personalizados, paquetes, precios y servicios.')
@section('canonical', $showPrices ? route('catalog.magazine.priced') : route('catalog.magazine.unpriced'))
@section('seo_image', asset('images/forjalab-hero.png'))

@section('content')
@php $totalItems = $products->count() + $bundles->count(); @endphp
<div class="magazine-experience" data-magazine>
    <header class="magazine-topbar"><a href="{{ route('home') }}"><img src="{{ asset('icon-192.png') }}" alt="" width="34" height="34"><strong>ForjaLab</strong></a><span>Catálogo digital · {{ $showPrices ? 'Con precios' : 'Sin precios' }} · {{ now()->format('Y') }}</span><div><button type="button" data-magazine-share aria-label="Compartir catálogo"><i class="bi bi-share-fill"></i><span>Compartir</span></button><button type="button" data-magazine-fullscreen aria-label="Pantalla completa"><i class="bi bi-arrows-fullscreen"></i></button><a href="{{ route('home') }}" aria-label="Cerrar catálogo"><i class="bi bi-x-lg"></i></a></div></header>

    <div class="magazine-stage" data-magazine-stage>
        <div class="magazine-book">
            <article class="magazine-page magazine-cover is-active" data-magazine-page>
                <img src="{{ asset('images/forjalab-hero.png') }}" alt="Selección de productos personalizados ForjaLab" fetchpriority="high">
                <div class="magazine-cover-shade"></div><div class="magazine-cover-content"><span>Edición {{ now()->format('Y') }} · {{ $showPrices ? 'Con precios' : 'Edición para compartir' }}</span><h1>Ideas que<br><em>se vuelven objeto.</em></h1><p>Productos personalizados, tecnología y piezas creadas para conectar.</p><div><b>{{ $totalItems }}</b><small>productos y paquetes</small></div></div><span class="magazine-cover-mark">FL</span>
            </article>

            <article class="magazine-page magazine-editorial" data-magazine-page>
                <div class="magazine-page-number">02</div><div class="magazine-editorial-copy"><span class="magazine-overline">Bienvenido</span><h2>Este catálogo está vivo.</h2><p>Se alimenta directamente de nuestro taller. Cuando agregamos un producto, actualizamos un precio o armamos un paquete, esta revista cambia con nosotros.</p><div class="magazine-editorial-icons"><span><i class="bi bi-hand-index-thumb-fill"></i><b>Desliza</b><small>para cambiar de página</small></span><span><i class="bi bi-bag-heart-fill"></i><b>Descubre</b><small>productos y paquetes</small></span><span><i class="bi bi-whatsapp"></i><b>Cotiza</b><small>directamente con nosotros</small></span></div></div>
                <div class="magazine-index"><span>Contenido</span>@if($bundles->isNotEmpty())<div><b>01</b><p><strong>Paquetes</strong><small>{{ $bundles->count() }} combinaciones listas</small></p></div>@endif<div><b>{{ $bundles->isNotEmpty() ? '02' : '01' }}</b><p><strong>Productos</strong><small>{{ $products->count() }} ideas para personalizar</small></p></div><div><b>{{ $bundles->isNotEmpty() ? '03' : '02' }}</b><p><strong>Hablemos</strong><small>Cotización y producción</small></p></div></div>
            </article>

            @foreach($bundles as $bundle)
            <article class="magazine-page magazine-product-page bundle-edition" data-magazine-page>
                <div class="magazine-product-photo">@if($bundle->image_url)<img src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}" loading="lazy">@else<div class="magazine-photo-placeholder"><i class="bi bi-box-seam-fill"></i></div>@endif<span>Paquete ForjaLab</span></div>
                <div class="magazine-product-copy"><span class="magazine-overline">Combinación especial · {{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><h2>{{ $bundle->name }}</h2><p>{{ $bundle->description ?: 'Una selección de productos pensada para regalar, equipar o compartir.' }}</p><div class="magazine-includes"><small>Este paquete incluye</small>@foreach($bundle->items as $item)<span><i class="bi bi-check2"></i>{{ $item->quantity }} × {{ $item->product?->name }}</span>@endforeach</div>@if($showPrices)<div class="magazine-price"><small>Precio público</small><strong>${{ number_format((float)$bundle->public_price,0) }}</strong><span>MXN</span></div>@else<div class="magazine-price magazine-price-quote"><small>Proyecto personalizable</small><strong>Cotizar</strong></div>@endif<a href="{{ $showPrices ? route('catalog.bundle.show',$bundle) : 'https://wa.me/525564442949?text='.rawurlencode('Hola, vi el paquete '.$bundle->name.' en el catálogo y quiero cotizarlo.') }}" @unless($showPrices) target="_blank" rel="noopener" @endunless>{{ $showPrices ? 'Ver todos los detalles' : 'Solicitar cotización' }} <i class="bi bi-arrow-up-right"></i></a></div>
            </article>
            @endforeach

            @foreach($products as $product)
            <article class="magazine-page magazine-product-page {{ $loop->iteration % 2 === 0 ? 'reverse' : '' }}" data-magazine-page>
                <div class="magazine-product-photo">@if($product->image_url)<img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">@else<div class="magazine-photo-placeholder"><i class="bi bi-stars"></i></div>@endif<span>{{ $product->badge ?: 'Personalizable' }}</span></div>
                <div class="magazine-product-copy"><span class="magazine-overline">Colección ForjaLab · {{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><h2>{{ $product->name }}</h2><p>{{ $product->description ?: 'Personaliza esta pieza con tu nombre, logo, imagen o idea.' }}</p>@if($product->specifications)<div class="magazine-spec"><i class="bi bi-info-circle-fill"></i>{{ $product->specifications }}</div>@endif @if($product->options->isNotEmpty())<div class="magazine-chips">@foreach($product->options->take(6) as $option)<span>{{ $option->name }}</span>@endforeach</div>@endif @if($showPrices)<div class="magazine-price"><small>Desde</small><strong>${{ number_format((float)$product->public_price,0) }}</strong><span>MXN</span></div>@else<div class="magazine-price magazine-price-quote"><small>Personaliza a tu medida</small><strong>Cotizar</strong></div>@endif<a href="{{ $showPrices ? route('catalog.show',$product) : 'https://wa.me/525564442949?text='.rawurlencode('Hola, vi '.$product->name.' en el catálogo y quiero cotizarlo.') }}" @unless($showPrices) target="_blank" rel="noopener" @endunless>{{ $showPrices ? 'Personalizar producto' : 'Solicitar cotización' }} <i class="bi bi-arrow-up-right"></i></a></div>
            </article>
            @endforeach

            <article class="magazine-page magazine-closing" data-magazine-page><div><img src="{{ asset('icon-192.png') }}" alt="ForjaLab" width="92" height="92"><span class="magazine-overline">Hagamos algo único</span><h2>¿Cuál idea<br>forjamos primero?</h2><p>Cuéntanos qué producto te gustó, cuántas piezas necesitas y cómo quieres personalizarlo.</p><a href="https://wa.me/525564442949?text={{ rawurlencode('Hola, vi el catálogo digital de ForjaLab y quiero cotizar un producto.') }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i>Cotizar por WhatsApp</a><small>forjalab.com.mx · Ciudad de México</small></div></article>
        </div>
    </div>

    <nav class="magazine-controls" aria-label="Navegación del catálogo"><button type="button" data-magazine-prev aria-label="Página anterior"><i class="bi bi-arrow-left"></i></button><div><span data-magazine-current>01</span><i></i><span data-magazine-total>01</span></div><button type="button" data-magazine-next aria-label="Página siguiente"><i class="bi bi-arrow-right"></i></button></nav>
    <div class="magazine-progress"><i data-magazine-progress></i></div>
</div>
@endsection
