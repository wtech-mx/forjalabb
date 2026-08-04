<div class="admin-menu">
    <div class="container">
        <div class="admin-menu-inner">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>
            @can('catalog.view')
                <a class="{{ request()->routeIs('admin.catalog.*') ? 'active' : '' }}" href="{{ route('admin.catalog.index') }}">
                    <i class="bi bi-bag-heart-fill"></i><span>Catalogo</span>
                </a>
                <a class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}" href="{{ route('admin.packages.index') }}">
                    <i class="bi bi-box-seam-fill"></i><span>Paquetes</span>
                </a>
            @endcan
            @can('users.view')
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people-fill"></i><span>Usuarios</span>
                </a>
            @endcan
            @can('roles.view')
                <a class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                    <i class="bi bi-shield-lock-fill"></i><span>Roles</span>
                </a>
            @endcan
        </div>
    </div>
</div>
