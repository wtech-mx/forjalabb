<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ForjaLab en CDMX: corte laser, impresion 3D, sublimacion, DTF, QR, NFC y software conectado.">
    <title>@yield('title', 'ForjaLab | Productos fabricados y conectados')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top navbar-glass">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <img class="brand-logo" src="{{ asset('images/logo.png') }}" alt="Productos personalizados con QR, NFC, sublimacion, DTF e impresion 3D">
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#lanzamiento">Lanzamiento</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Panel</a></li>
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
                        <a class="btn btn-dark btn-sm px-3" href="https://wa.me/?text=Hola%2C%20quiero%20cotizar%20un%20producto%20personalizado%20con%20QR%20o%20NFC" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-1"></i>Cotizar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer-band py-5">
        <div class="container d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <div class="fw-bold fs-5">ForjaLab</div>
                <p class="mb-0 text-secondary">Tu idea, fabricada y conectada en CDMX.</p>
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
