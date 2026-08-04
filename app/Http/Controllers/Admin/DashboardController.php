<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalProducts' => CatalogProduct::count(),
            'activeProducts' => CatalogProduct::where('is_active', true)->count(),
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'latestProducts' => CatalogProduct::latest()->take(6)->get(),
        ]);
    }
}
