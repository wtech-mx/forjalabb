<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CatalogBundleController;
use App\Http\Controllers\Admin\CatalogProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SmartTagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicTagController;
use App\Models\CatalogBundle;
use App\Models\CatalogProduct;
use Illuminate\Support\Facades\Route;

$services = [
    'biker-tag' => [
        'eyebrow' => 'Linea inicial',
        'title' => 'Biker Tag QR de emergencia',
        'short' => 'Identificacion fisica para motociclistas con perfil privado, contactos de emergencia y datos medicos esenciales.',
        'price' => '$449 a $699',
        'renewal' => '$149 a $299 anual',
        'hero_image' => 'biker-tag-hero.png',
        'accent' => 'warning',
        'audience' => 'Motociclistas, clubes y riders que necesitan una forma simple de compartir informacion critica cuando cada minuto cuenta.',
        'features' => [
            'Tag metalico o acrilico grabado con QR',
            'Perfil editable con alergias, tipo de sangre y contactos',
            'Boton directo de WhatsApp o llamada',
            'Activacion, desactivacion y cambio de propietario',
            'Opcion para motoclubes con registro por integrante',
            'Reposicion y renovacion anual del perfil',
        ],
        'packages' => [
            ['name' => 'Biker Esencial', 'range' => '$549-$799', 'items' => 'Tag QR, perfil privado, 2 contactos y activacion.'],
            ['name' => 'Biker Club', 'range' => '$1,099-$1,599', 'items' => 'Tag, parche/nombre, pagina de club y carga de miembros.'],
            ['name' => 'Motoclub', 'range' => 'Desde $4,500', 'items' => 'Lote de tags, panel, imagen del club y reposiciones.'],
        ],
    ],
    'dog-tags' => [
        'eyebrow' => 'Producto independiente',
        'title' => 'Dog Tags QR para mascotas',
        'short' => 'Placas personalizadas para mascotas con perfil editable, alerta al propietario y contacto rapido desde el celular.',
        'price' => '$249 a $549',
        'renewal' => '$149 a $299 anual',
        'hero_image' => 'dog-tags-hero.png',
        'accent' => 'success',
        'audience' => 'Familias, veterinarias, esteticas caninas y rescatistas que quieren identificacion bonita, durable y conectada.',
        'features' => [
            'Placa MDF, acrilico o 3D con QR grabado',
            'Perfil con nombre, foto, notas y datos del responsable',
            'Boton de contacto por WhatsApp',
            'Control de privacidad para mostrar solo lo necesario',
            'Cambio de propietario o datos sin fabricar otra placa',
            'Paquetes para una mascota o familia completa',
        ],
        'packages' => [
            ['name' => 'Pet Basico', 'range' => '$349-$499', 'items' => 'Placa QR, perfil editable y contacto principal.'],
            ['name' => 'Pet Smart', 'range' => '$599-$799', 'items' => 'Placa premium, foto, datos medicos y alerta por WhatsApp.'],
            ['name' => 'Pet Family', 'range' => '$999-$1,499', 'items' => 'Set de placas para varias mascotas y perfiles agrupados.'],
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

Route::get('/servicios/laser', function () {
    return view('laser');
})->name('services.laser');

Route::get('/catalogo/tequileros', function () {
    return view('catalog.tequileros');
})->name('catalog.tequileros');

Route::get('/catalogo/paquete-15-septiembre', function () {
    return view('catalog.paquete-15-septiembre');
})->name('catalog.package');

Route::get('/catalogo/porta-vasos', function () {
    return view('catalog.porta-vasos');
})->name('catalog.coasters');

Route::get('/catalogo/tazas', function () {
    return view('catalog.tazas');
})->name('catalog.mugs');

Route::get('/catalogo/vaso-cafe-ceramica', function () {
    return view('catalog.vaso-cafe-ceramica');
})->name('catalog.ceramic-cup');

Route::get('/catalogo/termo-color-mate', function () {
    return view('catalog.termo-color-mate');
})->name('catalog.matte-thermo');

Route::get('/catalogo/paquetes/{bundle:slug}', function (CatalogBundle $bundle) {
    abort_unless($bundle->is_active, 404);

    return view('catalog.bundle-show', [
        'bundle' => $bundle->load(['items.product', 'photos']),
    ]);
})->name('catalog.bundle.show');

Route::get('/catalogo/{catalogProduct:slug}', function (CatalogProduct $catalogProduct) {
    abort_unless($catalogProduct->is_active, 404);

    return view('catalog.show', [
        'product' => $catalogProduct->load(['costs', 'options', 'salePackages']),
    ]);
})->name('catalog.show');

Route::get('/servicios/{service}', function (string $service) use ($services) {
    abort_unless(isset($services[$service]), 404);

    return view('service', [
        'service' => $services[$service],
        'slug' => $service,
    ]);
})->name('services.show');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->middleware('can:dashboard.view')->name('dashboard');

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
