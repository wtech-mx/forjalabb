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

        collect([
            [
                'name' => 'Tabla + 2 tequileros + botella',
                'slug' => 'paquete-15-septiembre',
                'description' => 'Un set completo para regalo, celebracion o marca: tabla personalizada, dos tequileros y botella licorera con diseno coordinado.',
                'badge' => 'Paquete recomendado',
                'image_path' => 'images/catalog/paquete-15-septiembre.png',
                'url' => null,
                'action_label' => 'Ver paquete',
                'presentation' => CatalogProduct::PRESENTATION_PACKAGE,
                'is_featured' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Porta vasos',
                'slug' => 'porta-vasos',
                'description' => 'Set de 4 piezas con disenos patrios. Pueden ser iguales o combinados.',
                'image_path' => 'images/catalog/porta-vasos-viva-mexico-square.png',
                'url' => null,
                'action_label' => 'Ver galeria',
                'presentation' => CatalogProduct::PRESENTATION_PHOTO,
                'sort_order' => 20,
            ],
            [
                'name' => 'Tequileros personalizados',
                'slug' => 'tequileros-personalizados',
                'description' => 'Sets de 3 o 6 piezas en blanco, satinado o transparente con disenos oficiales.',
                'image_path' => 'images/catalog/aguacate-teq-transparent.png',
                'url' => null,
                'action_label' => 'Configurar',
                'presentation' => CatalogProduct::PRESENTATION_TEQUILA,
                'sort_order' => 30,
            ],
            [
                'name' => 'Tazas',
                'slug' => 'tazas',
                'description' => 'Tazas blancas con interior de color y disenos patrios para regalo o temporada.',
                'image_path' => 'images/catalog/taza-producto-studio.png',
                'url' => null,
                'action_label' => 'Configurar',
                'presentation' => CatalogProduct::PRESENTATION_PHOTO,
                'sort_order' => 40,
            ],
            [
                'name' => 'Vaso cafe ceramica',
                'slug' => 'vaso-cafe-ceramica',
                'description' => 'Vaso tipo cafe con tapa e interior de color, personalizable con los disenos oficiales.',
                'image_path' => 'images/catalog/vaso-cafe-producto-studio.png',
                'url' => null,
                'action_label' => 'Configurar',
                'presentation' => CatalogProduct::PRESENTATION_PHOTO,
                'sort_order' => 50,
            ],
            [
                'name' => 'Termo color mate',
                'slug' => 'termo-color-mate',
                'description' => 'Termos mate en negro, gris, verde o blanco para personalizar por temporada.',
                'image_path' => 'images/catalog/termo-mate-producto-studio.png',
                'url' => null,
                'action_label' => 'Configurar',
                'presentation' => CatalogProduct::PRESENTATION_PHOTO,
                'sort_order' => 60,
            ],
            [
                'name' => 'Botella licorera',
                'slug' => 'botella-licorera',
                'description' => 'Botella decorativa o de regalo con grabado, vinil o diseno aplicado.',
                'icon' => 'bottle',
                'action_label' => 'Cotizar',
                'presentation' => CatalogProduct::PRESENTATION_ICON,
                'sort_order' => 70,
            ],
            [
                'name' => 'Tabla',
                'slug' => 'tabla',
                'description' => 'Tabla personalizada para cocina, botanero, parrilla o kit de regalo.',
                'icon' => 'grid-3x3-gap',
                'action_label' => 'Cotizar',
                'presentation' => CatalogProduct::PRESENTATION_ICON,
                'sort_order' => 80,
            ],
            [
                'name' => 'Collar para perros o motos',
                'slug' => 'collar-para-perros-o-motos',
                'description' => 'Collar o placa con identidad visual, QR o datos de contacto segun el uso.',
                'icon' => 'tag',
                'action_label' => 'Cotizar',
                'presentation' => CatalogProduct::PRESENTATION_ICON,
                'sort_order' => 90,
            ],
        ])->each(fn (array $product) => CatalogProduct::updateOrCreate(
            ['slug' => $product['slug']],
            $product + ['is_active' => true]
        ));
    }
}
