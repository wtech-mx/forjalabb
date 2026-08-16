<?php

use App\Http\Controllers\Admin\CatalogBundleController;
use App\Http\Controllers\Admin\CatalogProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SmartTagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogMagazineController;
use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PublicTagController;
use App\Models\CatalogBundle;
use App\Models\CatalogProduct;
use Illuminate\Support\Facades\Route;

$services = [
    'biker-tag' => [
        'eyebrow' => 'Identificacion QR para bikers',
        'title' => 'Biker Tag QR de emergencia',
        'short' => 'Kit de dos placas militares personalizadas con QR: una para las llaves de la moto y otra para el motociclista.',
        'price_label' => 'Pago unico',
        'price' => '$250',
        'secondary_label' => 'Cambio de datos',
        'renewal' => '$50',
        'hero_image' => 'biker-tag-hero.png',
        'accent' => 'warning',
        'audience' => 'Para motociclistas que quieren llevar una identificacion fisica con QR. Al escanearlo se abre una landing con la informacion que el cliente decida compartir para contacto o emergencia.',
        'packages_heading' => 'Pago unico y actualizaciones simples.',
        'packages_intro' => 'El precio final depende del diseno de la cadena. Por eso la cotizacion se confirma por WhatsApp antes de fabricar.',
        'cta_heading' => 'Cotiza tu Biker Tag por WhatsApp.',
        'cta_text' => 'Mandanos la idea de diseno, nombre o estilo que quieres para la cadena y te confirmamos precio final antes de producir.',
        'cta_label' => 'Cotizar mi Biker Tag',
        'features' => [
            'Dos placas militares personalizadas: moto y portador',
            'QR vinculado a una landing individual',
            'Datos de contacto y emergencia segun lo que proporcione el cliente',
            'Pago unico de por vida, sin mensualidad',
            'Cotizacion por WhatsApp segun el diseno de la cadena',
            'Cambios de informacion posteriores por $50',
        ],
        'packages' => [
            ['name' => 'Kit Biker Tag', 'range' => '$250', 'items' => 'Dos placas militares QR: una para el llavero de la moto y otra para el portador.'],
            ['name' => 'Landing incluida', 'range' => 'De por vida', 'items' => 'Pagina individual conectada al QR con los datos proporcionados.'],
            ['name' => 'Cambio de datos', 'range' => '$50', 'items' => 'Actualizacion posterior de telefono, contacto, datos o informacion visible.'],
        ],
    ],
    'dog-tags' => [
        'eyebrow' => 'Identificacion QR para mascotas',
        'title' => 'Dog Tags QR para mascotas',
        'short' => 'Placa personalizada con QR que abre una landing privada con los datos importantes de tu mascota.',
        'price_label' => 'Pago unico',
        'price' => '$150 a $180',
        'secondary_label' => 'Cambio de datos',
        'renewal' => '$50',
        'hero_image' => 'dog-tags-hero.png',
        'accent' => 'success',
        'audience' => 'Para familias que quieren una placa bonita y util. Al escanear el QR se abre una landing con la informacion que el cliente decida compartir: nombre de la mascota, responsable, telefono y notas importantes.',
        'packages_heading' => 'Pago unico y cambios cuando los necesites.',
        'packages_intro' => 'El precio final depende del diseno de la placa o cadena. Por eso la cotizacion se confirma por WhatsApp antes de fabricar.',
        'cta_heading' => 'Cotiza tu Dog Tag QR por WhatsApp.',
        'cta_text' => 'Mandanos el nombre de tu mascota, estilo o referencia de diseno y te confirmamos el precio final antes de producir.',
        'cta_label' => 'Cotizar mi Dog Tag',
        'features' => [
            'Placa personalizada con diseno elegido por el cliente',
            'QR vinculado a una landing individual',
            'Datos de la mascota y responsable segun lo que proporcione el cliente',
            'Pago unico de por vida, sin mensualidad',
            'Cotizacion por WhatsApp segun el diseno de la placa',
            'Cambios de informacion posteriores por $50',
        ],
        'packages' => [
            ['name' => 'Placa QR', 'range' => '$150-$180', 'items' => 'Precio segun diseno, acabado y complejidad de la placa.'],
            ['name' => 'Landing incluida', 'range' => 'De por vida', 'items' => 'Pagina individual conectada al QR con los datos proporcionados.'],
            ['name' => 'Cambio de datos', 'range' => '$50', 'items' => 'Actualizacion posterior de telefono, responsable, notas o informacion visible.'],
        ],
    ],
];

Route::get('/', function () {
    return view('home', [
        'featuredCatalogProduct' => CatalogProduct::active()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->first(),
        'catalogProducts' => CatalogProduct::active()
            ->where('is_featured', false)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
        'catalogBundles' => CatalogBundle::active()
            ->with(['items.product'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
    ]);
})->name('home');

Route::get('/sitemap.xml', function () {
    $urls = collect([
        ['loc' => route('home'), 'lastmod' => null],
        ['loc' => route('services.show', 'biker-tag'), 'lastmod' => null],
        ['loc' => route('services.show', 'dog-tags'), 'lastmod' => null],
        ['loc' => route('services.laser'), 'lastmod' => null],
        ['loc' => route('services.sublimation'), 'lastmod' => null],
        ['loc' => route('catalog.magazine.priced'), 'lastmod' => null],
        ['loc' => route('catalog.magazine.unpriced'), 'lastmod' => null],
    ])->concat(
        CatalogProduct::active()->get(['slug', 'updated_at'])->map(fn (CatalogProduct $product) => [
            'loc' => route('catalog.show', $product),
            'lastmod' => $product->updated_at?->toAtomString(),
        ])
    )->concat(
        CatalogBundle::active()->get(['slug', 'updated_at'])->map(fn (CatalogBundle $bundle) => [
            'loc' => route('catalog.bundle.show', $bundle),
            'lastmod' => $bundle->updated_at?->toAtomString(),
        ])
    );

    return response()
        ->view('sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

Route::get('/robots.txt', fn () => response(
    "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /t/\nSitemap: ".route('sitemap')."\n",
    200,
    ['Content-Type' => 'text/plain; charset=UTF-8']
))->name('robots');

Route::get('/servicios/laser', function () {
    return view('laser');
})->name('services.laser');

Route::get('/servicios/sublimacion', function () {
    return view('sublimation');
})->name('services.sublimation');

Route::get('/catalogo-digital', fn () => redirect()->route('catalog.magazine.priced'))->name('catalog.magazine');
Route::get('/catalogo-digital/con-precios', CatalogMagazineController::class)->defaults('showPrices', true)->name('catalog.magazine.priced');
Route::get('/catalogo-digital/sin-precios', CatalogMagazineController::class)->defaults('showPrices', false)->name('catalog.magazine.unpriced');

Route::get('/catalogo/paquetes/{bundle:slug}', function (CatalogBundle $bundle) {
    abort_unless($bundle->is_active, 404);

    return view('catalog.bundle-show', [
        'bundle' => $bundle->load(['items.product', 'photos']),
    ]);
})->name('catalog.bundle.show');

Route::get('/catalogo/{catalogProduct:slug}', function (CatalogProduct $catalogProduct) {
    abort_unless($catalogProduct->is_active, 404);

    return view('catalog.show', [
        'product' => $catalogProduct->load(['costs', 'options', 'salePackages', 'photos']),
    ]);
})->name('catalog.show');

Route::get('/servicios/{service}', function (string $service) use ($services) {
    abort_unless(isset($services[$service]), 404);

    return view(match ($service) {
        'biker-tag' => 'services.biker-tag',
        'dog-tags' => 'services.dog-tags',
        default => 'service',
    }, [
        'service' => $services[$service],
        'slug' => $service,
    ]);
})->name('services.show');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/analytics/events', [AnalyticsController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('analytics.events');
Route::post('/prospectos', [LeadController::class, 'store'])->middleware('throttle:10,1')->name('leads.store');
Route::get('/correo/open/{token}.gif', [EmailTrackingController::class, 'open'])->middleware('throttle:120,1')->name('mailing.track.open');
Route::get('/correo/click/{token}', [EmailTrackingController::class, 'click'])->middleware(['signed', 'throttle:120,1'])->name('mailing.track.click');

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/orders/{order}/pdf', [OrderController::class, 'pdf'])->middleware('can:orders.view')->name('orders.pdf');
    Route::resource('orders', OrderController::class)->except(['index', 'show', 'destroy'])->middleware('can:orders.manage');
    Route::resource('orders', OrderController::class)->only(['index', 'show'])->middleware('can:orders.view');

    Route::resource('customers', CustomerController::class)->only(['index'])->middleware('can:customers.view');
    Route::resource('customers', CustomerController::class)->only(['update'])->middleware('can:customers.manage');

    Route::post('/mailing/{mailing}/send', [EmailCampaignController::class, 'send'])->name('mailing.send');
    Route::post('/mailing/{mailing}/resend', [EmailCampaignController::class, 'resend'])->name('mailing.resend');
    Route::get('/mailing/{mailing}/preview', [EmailCampaignController::class, 'preview'])->name('mailing.preview');
    Route::resource('mailing', EmailCampaignController::class)->except('show');

    Route::get('/tags/{tag}/qr', [SmartTagController::class, 'qr'])->name('tags.qr');
    Route::resource('tags', SmartTagController::class)->except('destroy');

    Route::resource('catalog', CatalogProductController::class)
        ->parameters(['catalog' => 'catalog'])
        ->only('index')
        ->middleware('can:catalog.view');
    Route::resource('catalog', CatalogProductController::class)
        ->parameters(['catalog' => 'catalog'])
        ->except(['index', 'show'])
        ->middleware('can:catalog.manage');
    Route::get('/catalog/{catalog}/preview', [CatalogProductController::class, 'preview'])
        ->middleware('can:catalog.view')
        ->name('catalog.preview');

    Route::resource('packages', CatalogBundleController::class)
        ->parameters(['packages' => 'package'])
        ->only('index')
        ->middleware('can:catalog.view');
    Route::resource('packages', CatalogBundleController::class)
        ->parameters(['packages' => 'package'])
        ->except(['index', 'show'])
        ->middleware('can:catalog.manage');
    Route::get('/packages/{package}/preview', [CatalogBundleController::class, 'preview'])
        ->middleware('can:catalog.view')
        ->name('packages.preview');

    Route::resource('users', UserController::class)
        ->only('index')
        ->middleware('can:users.view');
    Route::resource('users', UserController::class)
        ->except(['index', 'show'])
        ->middleware('can:users.manage');

    Route::resource('roles', RoleController::class)
        ->only('index')
        ->middleware('can:roles.view');
    Route::resource('roles', RoleController::class)
        ->except(['index', 'show'])
        ->middleware('can:roles.manage');
});

Route::get('/t/{token}', PublicTagController::class)->name('tags.public');
Route::post('/t/{token}/scan', [PublicTagController::class, 'scan'])->name('tags.scan');
