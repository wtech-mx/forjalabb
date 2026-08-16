<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'group', 'description'])]
class Permission extends Model
{
    use HasFactory;

    public const CATALOG = [
        'Panel' => [
            ['dashboard.view', 'Ver dashboard'],
        ],
        'Usuarios' => [
            ['users.view', 'Ver usuarios'],
            ['users.manage', 'Crear y editar usuarios'],
        ],
        'Roles' => [
            ['roles.view', 'Ver roles'],
            ['roles.manage', 'Crear y editar roles'],
        ],
        'Catalogo' => [
            ['catalog.view', 'Ver catalogo'],
            ['catalog.manage', 'Crear y editar productos'],
        ],
        'Pedidos' => [
            ['orders.view', 'Ver pedidos y descargar PDF'],
            ['orders.manage', 'Crear y editar pedidos y clientes'],
        ],
        'Clientes' => [
            ['customers.view', 'Ver clientes y prospectos'],
            ['customers.manage', 'Actualizar seguimiento de prospectos'],
        ],
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
