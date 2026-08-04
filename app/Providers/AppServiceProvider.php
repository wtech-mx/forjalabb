<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        collect(Permission::CATALOG)
            ->flatten(1)
            ->each(fn (array $permission) => Gate::define(
                $permission[0],
                fn ($user) => $user->hasPermissionTo($permission[0])
            ));
    }
}
