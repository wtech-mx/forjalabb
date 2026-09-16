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
        'Smart Tags' => [
            ['tags.view', 'Ver Smart Tags'],
            ['tags.manage', 'Crear y editar Smart Tags'],
        ],
        'Usuarios' => [
            ['users.view', 'Ver usuarios'],
            ['users.manage', 'Crear y editar usuarios'],
        ],
        'Roles' => [
            ['roles.view', 'Ver roles'],
            ['roles.manage', 'Crear y editar roles'],
        ],
        'Configuracion' => [
            ['settings.manage', 'Descargar y restaurar respaldos de base de datos'],
        ],
        'Productos' => [
            ['catalog.view', 'Ver productos'],
            ['catalog.manage', 'Crear y editar productos'],
        ],
        'Paquetes' => [
            ['packages.view', 'Ver paquetes'],
            ['packages.manage', 'Crear y editar paquetes'],
        ],
        'Inventario' => [
            ['inventory.view', 'Ver inventario'],
            ['inventory.manage', 'Ajustar existencias y variantes'],
        ],
        'Pedidos' => [
            ['orders.view', 'Ver pedidos y descargar PDF'],
            ['orders.manage', 'Crear y editar pedidos y clientes'],
        ],
        'Galeria Drive' => [
            ['drive-gallery.view', 'Ver y descargar archivos de Drive'],
            ['drive-gallery.manage', 'Subir, organizar y eliminar archivos de Drive'],
        ],
        'WhatsApp' => [
            ['whatsapp.view', 'Ver conversaciones de WhatsApp'],
            ['whatsapp.manage', 'Enviar mensajes por WhatsApp'],
        ],
        'Entregas' => [
            ['deliveries.view', 'Ver mapa y rutas de entrega'],
            ['deliveries.manage', 'Actualizar ubicaciones de entrega'],
        ],
        'Reportes' => [
            ['reports.view', 'Ver reportes de ventas'],
            ['commissions.view', 'Ver y descargar reportes de comisiones'],
        ],
        'Gastos' => [
            ['expenses.view', 'Ver gastos'],
            ['expenses.manage', 'Registrar y eliminar gastos'],
        ],
        'Envios' => [
            ['shipments.view', 'Ver envios y cotizaciones'],
            ['shipments.manage', 'Crear guias y administrar envios'],
        ],
        'Clientes' => [
            ['customers.view', 'Ver clientes y prospectos'],
            ['customers.manage', 'Actualizar seguimiento de prospectos'],
        ],
        'Mailing' => [
            ['mailing.view', 'Ver campanas de correo'],
            ['mailing.manage', 'Crear, editar y enviar campanas'],
        ],
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
