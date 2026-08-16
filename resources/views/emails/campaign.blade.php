<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $campaign->subject }}</title><style>
@media only screen and (max-width:600px){
body,table,td,a{-webkit-text-size-adjust:100%!important}.fl-header,.fl-featured,.fl-related-grid,.fl-footer{width:100%!important}.fl-header-logo,.fl-header-brand,.fl-header-info,.fl-featured-media,.fl-featured-copy,.fl-related-cell{display:block!important;width:100%!important;box-sizing:border-box!important}.fl-header-logo{padding:20px 0 5px!important}.fl-header-logo img{margin:0 auto!important}.fl-header-brand,.fl-header-info{text-align:center!important;padding:4px 12px!important}.fl-header-info{padding-bottom:18px!important}.fl-featured-media>img{height:auto!important;max-height:380px!important}.fl-featured-copy{padding:24px 12px!important;text-align:center!important}.fl-featured-copy h2{font-size:25px!important}.fl-product-button,.fl-whatsapp-button{display:block!important;width:auto!important;margin:0 auto!important;text-align:center!important;white-space:normal!important}.fl-related-cell{padding:0 0 12px!important}.fl-related-empty,.fl-related-spacer{display:none!important}.fl-related-image{width:38%!important}.fl-related-copy{padding:14px!important}.fl-related-copy strong{font-size:14px!important}.fl-footer td{padding:28px 16px!important}.fl-socials a{display:inline-block!important;margin:5px 7px!important}
}
</style></head>
<body style="margin:0;background:#f3eee7;color:#2b1d15;font-family:Arial,sans-serif">
@php
    $personalizedContent = str_replace('{{nombre}}', e($recipientName ?: 'cliente'), $campaign->content_html ?: '');
    $imagesRoot = rtrim(url('/images'), '/').'/';
    $personalizedContent = preg_replace('~(["\'])(?:https?://[^/"\']+)?/admin/images/~i', '$1'.$imagesRoot, $personalizedContent);
    $personalizedContent = preg_replace('~(src=["\'])(?:\.\./)*images/~i', '$1'.$imagesRoot, $personalizedContent);
    $personalizedContent = preg_replace('~<table(?![^>]*class=)([^>]*data-forjalab-header="1")~i', '<table class="fl-header"$1', $personalizedContent);
    $personalizedContent = preg_replace('~<table(?![^>]*class=)([^>]*data-forjalab-footer="1")~i', '<table class="fl-footer"$1', $personalizedContent);
    $personalizedContent = preg_replace('~<table(?![^>]*class=)([^>]*data-forjalab-product="1"[^>]*margin:30px)~i', '<table class="fl-featured"$1', $personalizedContent);
    $personalizedContent = preg_replace('~<table(?![^>]*class=)([^>]*data-forjalab-product="1"[^>]*margin:15px)~i', '<table class="fl-related-grid"$1', $personalizedContent);
    $featuredUrl = $featured instanceof \App\Models\CatalogProduct ? route('catalog.show',$featured) : ($featured instanceof \App\Models\CatalogBundle ? route('catalog.bundle.show',$featured) : null);
    $contentIncludesProducts = str_contains($campaign->content_html ?: '', 'data-forjalab-product');
    $contentIncludesHeader = str_contains($campaign->content_html ?: '', 'data-forjalab-header');
    $contentIncludesFooter = str_contains($campaign->content_html ?: '', 'data-forjalab-footer');
    $featuredGallery = $featured ? collect([$featured->image_url])->merge($featured->photos?->map->image_url ?? [])->filter()->unique()->values() : collect();
    $featuredKind = $featured instanceof \App\Models\CatalogBundle ? 'paquete' : 'producto';
    $featuredWhatsapp = $featured ? 'https://wa.me/525564442949?text='.rawurlencode('Hola, vi el '.$featuredKind.' '.$featured->name.' en un correo de ForjaLab y quiero recibir más información.') : null;
@endphp
<div style="display:none;max-height:0;overflow:hidden;opacity:0">{{ $campaign->preview_text }}</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3eee7"><tr><td align="center" style="padding:24px 10px">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="width:100%;max-width:640px;background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 12px 35px rgba(59,35,20,.12)">
@unless($contentIncludesHeader)<tr><td style="padding:22px 28px;background:#211711;color:#fff"><table role="presentation" width="100%"><tr><td><img src="{{ asset('icon-192.png') }}" width="48" height="48" alt="ForjaLab" style="display:block;border-radius:50%;background:#fff"></td><td align="right"><strong style="font-size:22px">ForjaLab</strong><br><span style="color:#d9c7b9;font-size:12px">Ideas que se vuelven objeto</span></td></tr></table></td></tr>@endunless
<tr><td style="padding:{{ $contentIncludesHeader ? '0 34px' : '32px 34px' }};font-size:16px;line-height:1.65">{!! $personalizedContent !!}</td></tr>
@if($featured && ! $contentIncludesProducts)
<tr><td style="padding:0 28px 28px"><table role="presentation" width="100%" style="background:#fff"><tr>@if($featured->image_url)<td width="52%" valign="middle"><img src="{{ $featured->image_url }}" alt="{{ $featured->name }}" width="290" style="display:block;width:100%;height:280px;object-fit:cover">@if($featuredGallery->count()>1)<table role="presentation" width="100%" cellspacing="4" style="margin-top:5px"><tr>@foreach($featuredGallery->skip(1)->take(4) as $image)<td width="25%"><img src="{{ $image }}" alt="{{ $featured->name }}" style="display:block;width:100%;height:58px;object-fit:cover"></td>@endforeach</tr></table>@endif</td>@endif<td style="padding:24px 8px 24px 30px"><span style="color:#897b70;font-size:11px;font-weight:bold;text-transform:uppercase">Selección ForjaLab</span><h2 style="margin:8px 0;font-size:26px;line-height:1.05">{{ $featured->name }}</h2><p style="color:#71645a;font-size:14px;line-height:1.5">{{ \Illuminate\Support\Str::limit($featured->description,180) }}</p>@if((float)$featured->public_price>0)<strong style="display:block;margin:14px 0;font-size:24px">${{ number_format((float)$featured->public_price,0) }} MXN</strong>@endif<a href="{{ $featuredWhatsapp }}" style="display:inline-block;padding:11px 18px;color:#fff;text-decoration:none;font-weight:bold;background:#25d366;border-radius:99px">Información del producto</a></td></tr></table></td></tr>
@endif
@if($related->isNotEmpty() && ! $contentIncludesProducts)
<tr><td style="padding:8px 28px 30px"><h2 style="margin:0 0 16px;text-align:center;font-size:22px">También te puede interesar</h2><table role="presentation" width="100%" cellspacing="8"><tr>@foreach($related->take(3) as $product)<td width="33.33%" valign="top" style="padding:8px;text-align:center;background:#faf6f0;border-radius:10px">@if($product->image_url)<img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="150" style="display:block;width:100%;height:120px;object-fit:cover;border-radius:8px">@endif<strong style="display:block;margin:9px 0;font-size:13px">{{ $product->name }}</strong><a href="{{ route('catalog.show',$product) }}" style="color:#d75e10;font-size:12px;font-weight:bold">Conocer más →</a></td>@endforeach</tr></table></td></tr>
@endif
@unless($contentIncludesFooter)<tr><td align="center" style="padding:26px;background:#211711;color:#cdbbae"><strong style="display:block;color:#fff;font-size:18px">ForjaLab</strong><p style="margin:8px 0;font-size:12px">Productos personalizados · QR · NFC · Láser · Textil</p><a href="https://wa.me/525564442949" style="color:#ff984d;font-weight:bold;text-decoration:none">WhatsApp 55 6444 2949</a><p style="margin:16px 0 0;font-size:10px;color:#917d6f">Recibiste este correo porque compartiste tus datos con ForjaLab.</p></td></tr>@endunless
</table></td></tr></table>
@if($recipient?->tracking_token)<img src="{{ route('mailing.track.open',['token'=>$recipient->tracking_token]) }}" width="1" height="1" alt="" style="display:block;width:1px;height:1px;border:0;overflow:hidden">@endif
</body></html>
