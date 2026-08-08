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
<body>
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#textil">Textil</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('services.laser') }}">Laser</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#valor">Valor</a></li>
                    @auth
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
        </div>
    </footer>
</body>
</html>
