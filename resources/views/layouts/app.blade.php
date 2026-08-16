<!doctype html>
<html lang="es">
<head>
    @php
        $isPrivatePage = request()->routeIs('admin.*', 'login');
        $seoTitle = trim($__env->yieldContent('title', 'ForjaLab | Productos personalizados en CDMX'));
        $seoDescription = trim($__env->yieldContent('meta_description', 'ForjaLab crea productos personalizados en CDMX: placas QR para mascotas y motociclistas, corte laser, impresion 3D, sublimacion y DTF.'));
        $seoCanonical = trim($__env->yieldContent('canonical', url()->current()));
        $seoImage = trim($__env->yieldContent('seo_image', asset('images/forjalab-hero.png')));
        $seoType = trim($__env->yieldContent('seo_type', 'website'));
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @unless ($isPrivatePage)
        <meta name="analytics-endpoint" content="{{ route('analytics.events') }}">
    @endunless
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#1b120b">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $isPrivatePage ? 'noindex, nofollow, noarchive' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' }}">
    @unless ($isPrivatePage)
        <link rel="canonical" href="{{ $seoCanonical }}">
        <meta property="og:locale" content="es_MX">
        <meta property="og:type" content="{{ $seoType }}">
        <meta property="og:site_name" content="ForjaLab">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoCanonical }}">
        <meta property="og:image" content="{{ $seoImage }}">
        <meta property="og:image:alt" content="{{ $seoTitle }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        <meta name="twitter:image" content="{{ $seoImage }}">
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => 'ForjaLab',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'image' => $seoImage,
            'telephone' => '+52 55 6444 2949',
            'areaServed' => ['@type' => 'City', 'name' => 'Ciudad de Mexico'],
            'contactPoint' => ['@type' => 'ContactPoint', 'telephone' => '+52 55 6444 2949', 'contactType' => 'sales', 'availableLanguage' => 'Spanish'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @yield('structured_data')
    @endunless
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ request()->routeIs('admin.*') ? 'admin-page' : '' }} {{ request()->routeIs('catalog.magazine*') ? 'magazine-page-body' : '' }}">
    <nav class="navbar navbar-expand-lg fixed-top navbar-glass">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <img class="brand-mark brand-mark-image" src="{{ asset('icon-192.png') }}" alt="" width="40" height="40" aria-hidden="true" decoding="async">
                ForjaLab
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#catalogo">Catalogo</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('catalog.magazine.priced') }}"><i class="bi bi-journal-richtext me-1"></i>Revista</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('services.sublimation') }}">Sublimación</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('services.laser') }}">Laser</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#valor">Valor</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link admin-panel-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Panel</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-dark btn-sm" type="submit">
                                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                                </button>
                            </form>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="btn btn-dark btn-sm px-3" href="https://wa.me/525564442949?text=Hola%2C%20quiero%20cotizar%20un%20producto%20personalizado%20con%20QR%20o%20NFC" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-1"></i>Cotizar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @auth
        @if (request()->routeIs('admin.*'))
            @include('admin.partials.menu')
        @endif
    @endauth

    <main>
        @if (request()->routeIs('admin.*'))
            <div class="admin-flash container">
                @if (session('status'))
                    <div class="alert alert-success mb-0">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger mb-0">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="footer-band py-5">
        <div class="container d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <div class="fw-bold fs-5">ForjaLab</div>
                <p class="mb-0 text-secondary">Personalización con diseño, oficio y tecnología.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-light">Laser</span>
                <span class="badge text-bg-light">3D</span>
                <span class="badge text-bg-light">Sublimacion</span>
                <span class="badge text-bg-light">DTF</span>
                <span class="badge text-bg-light">QR/NFC</span>
                <span class="badge text-bg-light">Software</span>
            </div>
            <div class="footer-socials" aria-label="Redes sociales de ForjaLab">
                <a href="https://www.facebook.com/share/19UaksSXGK/" target="_blank" rel="noopener noreferrer" aria-label="ForjaLab en Facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/forjalabby?igsh=ZDdnMGEzcWNpbndr" target="_blank" rel="noopener noreferrer" aria-label="ForjaLab en Instagram"><i class="bi bi-instagram"></i></a>
                <a href="https://www.tiktok.com/@forjalab3?_r=1&amp;_t=ZS-98spMLEz1FN" target="_blank" rel="noopener noreferrer" aria-label="ForjaLab en TikTok"><i class="bi bi-tiktok"></i></a>
            </div>
        </div>
    </footer>

    @unless (request()->routeIs('admin.*', 'catalog.magazine*'))
        @guest
        <div class="lead-popup" data-lead-popup data-endpoint="{{ route('leads.store') }}" aria-hidden="true">
            <div class="lead-popup-backdrop" data-lead-close></div>
            <section class="lead-popup-dialog" role="dialog" aria-modal="true" aria-labelledby="leadPopupTitle">
                <button class="lead-popup-close" type="button" aria-label="Cerrar promoción" data-lead-close><i class="bi bi-x-lg"></i></button>
                <div class="lead-popup-visual"><img src="{{ asset('images/forjalab-hero.png') }}" alt="Productos personalizados creados por ForjaLab" width="900" height="700"><span><i class="bi bi-gift-fill"></i> Beneficio de bienvenida</span></div>
                <div class="lead-popup-content">
                    <div data-lead-form-wrap><span class="lead-popup-kicker">Tu primera idea comienza aquí</span><h2 id="leadPopupTitle"><strong>10%</strong> de descuento</h2><p>Déjanos tus datos y recibe el beneficio en tu primera compra con ForjaLab.</p>
                        <form data-lead-form><div class="lead-form-grid"><label><span>Nombre *</span><input class="form-control" name="name" required maxlength="160" autocomplete="name"></label><label><span>Correo *</span><input class="form-control" name="email" type="email" required maxlength="160" autocomplete="email"></label><label><span>Teléfono *</span><input class="form-control" name="phone" type="tel" required maxlength="30" autocomplete="tel"></label><label><span>WhatsApp *</span><input class="form-control" name="whatsapp" type="tel" required maxlength="30"></label><label><span>Empresa <small>(opcional)</small></span><input class="form-control" name="company" maxlength="160" autocomplete="organization"></label><label><span>¿Qué servicio te interesa? *</span><select class="form-select" name="interested_service" required><option value="">Selecciona</option><option value="biker_tag">Biker Tag QR</option><option value="dog_tag">Dog Tag QR</option><option value="sublimation">Sublimación o DTF</option><option value="laser">Grabado y corte láser</option><option value="catalog">Productos del catálogo</option><option value="corporate">Pedido corporativo</option><option value="other">Otro proyecto</option></select></label></div><label class="lead-consent"><input type="checkbox" required> <span>Acepto que ForjaLab me contacte para atender mi solicitud y aplicar el descuento.</span></label><button class="btn btn-dark btn-lg w-100" type="submit" data-lead-submit><i class="bi bi-ticket-perforated-fill me-2"></i>Quiero mi 10% de descuento</button><small class="lead-form-status" data-lead-status></small></form>
                    </div>
                    <div class="lead-popup-success" data-lead-success hidden><i class="bi bi-check-circle-fill"></i><h2>¡Beneficio reservado!</h2><p>Registramos tus datos. Nuestro equipo te contactará para ayudarte con tu primera compra.</p><button class="btn btn-dark" type="button" data-lead-close>Seguir explorando</button></div>
                </div>
            </section>
        </div>
        @endguest

        <div class="social-chat" data-social-chat>
            <div class="social-chat-menu" id="socialChatMenu" aria-hidden="true">
                <div class="social-chat-heading"><span><strong>¿Hablamos?</strong><small>Encuentra a ForjaLab</small></span></div>
                <a class="facebook" href="https://www.facebook.com/share/19UaksSXGK/" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i><span>Facebook</span></a>
                <a class="instagram" href="https://www.instagram.com/forjalabby?igsh=ZDdnMGEzcWNpbndr" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i><span>Instagram</span></a>
                <a class="tiktok" href="https://www.tiktok.com/@forjalab3?_r=1&amp;_t=ZS-98spMLEz1FN" target="_blank" rel="noopener noreferrer"><i class="bi bi-tiktok"></i><span>TikTok</span></a>
                <a class="whatsapp" href="https://wa.me/525564442949?text=Hola%2C%20quiero%20informacion%20sobre%20ForjaLab" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp"></i><span>WhatsApp</span></a>
            </div>
            <button class="social-chat-trigger" type="button" aria-label="Abrir redes sociales" aria-expanded="false" aria-controls="socialChatMenu" data-social-chat-trigger><i class="bi bi-chat-heart-fill"></i><span>Contactanos</span></button>
        </div>
    @endunless
</body>
</html>
