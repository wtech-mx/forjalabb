<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $definitions = collect(Permission::CATALOG)->flatMap(function (array $items, string $group) {
            return collect($items)->map(fn (array $item) => [
                'slug' => $item[0],
                'name' => $item[1],
                'group' => $group,
            ]);
        });

        foreach ($definitions as $definition) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $definition['slug']],
                $definition + ['description' => null, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $permissionIds = DB::table('permissions')->pluck('id', 'slug');
        $roleIds = DB::table('roles')->pluck('id');

        $inheritance = [
            'catalog.view' => ['packages.view', 'inventory.view'],
            'catalog.manage' => ['packages.manage', 'inventory.manage'],
            'orders.view' => [
                'drive-gallery.view', 'whatsapp.view', 'deliveries.view', 'reports.view',
                'commissions.view', 'expenses.view', 'shipments.view',
            ],
            'orders.manage' => [
                'drive-gallery.manage', 'whatsapp.manage', 'deliveries.manage',
                'expenses.manage', 'shipments.manage',
            ],
        ];

        foreach ($inheritance as $source => $targets) {
            if (! isset($permissionIds[$source])) {
                continue;
            }

            $allowedRoleIds = DB::table('permission_role')
                ->where('permission_id', $permissionIds[$source])
                ->pluck('role_id');

            foreach ($allowedRoleIds as $roleId) {
                foreach ($targets as $target) {
                    if (isset($permissionIds[$target])) {
                        DB::table('permission_role')->insertOrIgnore([
                            'permission_id' => $permissionIds[$target],
                            'role_id' => $roleId,
                        ]);
                    }
                }
            }
        }

        // Estos modulos antes estaban disponibles para cualquier usuario autenticado.
        foreach ($roleIds as $roleId) {
            foreach (['tags.view', 'tags.manage', 'mailing.view', 'mailing.manage'] as $slug) {
                if (isset($permissionIds[$slug])) {
                    DB::table('permission_role')->insertOrIgnore([
                        'permission_id' => $permissionIds[$slug],
                        'role_id' => $roleId,
                    ]);
                }
            }
        }

        $superAdminId = DB::table('roles')->where('slug', 'super-admin')->value('id');
        if ($superAdminId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $superAdminId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $introducedSlugs = [
            'tags.view', 'tags.manage', 'packages.view', 'packages.manage',
            'inventory.view', 'inventory.manage', 'drive-gallery.view', 'drive-gallery.manage',
            'whatsapp.view', 'whatsapp.manage', 'deliveries.view', 'deliveries.manage',
            'reports.view', 'commissions.view', 'expenses.view', 'expenses.manage',
            'shipments.view', 'shipments.manage', 'mailing.view', 'mailing.manage',
        ];

        $ids = DB::table('permissions')->whereIn('slug', $introducedSlugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
};
