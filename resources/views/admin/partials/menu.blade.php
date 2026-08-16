<button class="admin-sidebar-toggle btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar"><i class="bi bi-grid-fill me-2"></i>Menu del panel</button>

<aside class="offcanvas-lg offcanvas-start admin-sidebar" id="adminSidebar" tabindex="-1" aria-labelledby="adminSidebarLabel">
    <div class="offcanvas-header"><div><small>Administracion</small><h2 class="offcanvas-title" id="adminSidebarLabel">ForjaLab</h2></div><button class="btn-close" type="button" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Cerrar"></button></div>
    <div class="offcanvas-body">
        <nav class="admin-sidebar-nav" aria-label="Menu administrativo">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i><span><strong>Panel</strong><small>Resumen y metricas</small></span></a>
            <a class="{{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" href="{{ route('admin.tags.index') }}"><i class="bi bi-qr-code-scan"></i><span><strong>Smart Tags</strong><small>Biker y Dog Tags</small></span></a>
            @can('orders.view')
                <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt-cutoff"></i><span><strong>Pedidos</strong><small>Clientes, pagos y PDF</small></span></a>
            @endcan
            @can('customers.view')
                <a class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><i class="bi bi-person-lines-fill"></i><span><strong>Clientes</strong><small>Clientes y prospectos web</small></span></a>
            @endcan
            @can('catalog.view')
                <a class="{{ request()->routeIs('admin.catalog.*') ? 'active' : '' }}" href="{{ route('admin.catalog.index') }}"><i class="bi bi-bag-heart-fill"></i><span><strong>Catalogo</strong><small>Productos y precios</small></span></a>
                <a class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}" href="{{ route('admin.packages.index') }}"><i class="bi bi-box-seam-fill"></i><span><strong>Paquetes</strong><small>Combos de productos</small></span></a>
                <a href="{{ route('catalog.magazine.priced') }}" target="_blank"><i class="bi bi-tags-fill"></i><span><strong>Revista con precios</strong><small>Catálogo para venta directa</small></span></a>
                <a href="{{ route('catalog.magazine.unpriced') }}" target="_blank"><i class="bi bi-eye-slash-fill"></i><span><strong>Revista sin precios</strong><small>Catálogo para cotización</small></span></a>
            @endcan
            @can('users.view')<a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people-fill"></i><span><strong>Usuarios</strong><small>Cuentas administrativas</small></span></a>@endcan
            @can('roles.view')<a class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock-fill"></i><span><strong>Roles</strong><small>Permisos y accesos</small></span></a>@endcan
        </nav>
        <div class="admin-sidebar-footer"><a href="{{ route('home') }}"><i class="bi bi-box-arrow-up-right"></i>Ver sitio publico</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="bi bi-box-arrow-left"></i>Cerrar sesion</button></form></div>
    </div>
</aside>
