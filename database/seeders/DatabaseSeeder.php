<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\CatalogProduct;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = collect(Permission::CATALOG)
            ->flatMap(function (array $items, string $group) {
                return collect($items)->map(fn (array $item) => [
                    'slug' => $item[0],
                    'name' => $item[1],
                    'group' => $group,
                ]);
            })
            ->mapWithKeys(function (array $permission) {
                return [
                    $permission['slug'] => Permission::updateOrCreate(
                        ['slug' => $permission['slug']],
                        $permission + ['description' => null]
                    ),
                ];
            });

        Permission::query()
            ->whereNotIn('slug', $permissions->keys())
            ->delete();

        $superAdmin = Role::updateOrCreate(
            ['slug' => Role::SUPER_ADMIN],
            [
                'name' => 'Super admin',
                'description' => 'Acceso completo a todas las secciones administrativas.',
            ]
        );
        $superAdmin->permissions()->sync($permissions->pluck('id'));

        $operator = Role::updateOrCreate(
            ['slug' => 'operador'],
            [
                'name' => 'Operador',
                'description' => 'Gestiona tags QR/NFC y consulta el dashboard.',
            ]
        );
        $operator->permissions()->sync(
            $permissions
                ->only(['dashboard.view', 'catalog.view'])
                ->pluck('id')
        );

        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@forjalab.test')],
            [
                'name' => env('ADMIN_NAME', 'Admin ForjaLab'),
                'password' => env('ADMIN_PASSWORD', 'password'),
            ]
        );

        if (! $admin->roles()->exists()) {
            $admin->roles()->attach($superAdmin);
        }

    }
}
