<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()
                ->withCount(['users', 'permissions'])
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', [
            'role' => new Role(),
            'permissions' => Permission::orderBy('group')->orderBy('name')->get()->groupBy('group'),
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        $role = Role::create($data);
        $role->permissions()->sync($permissions);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Rol creado correctamente.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.form', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::orderBy('group')->orderBy('name')->get()->groupBy('group'),
            'selectedPermissions' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $this->validated($request, $role);
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        if (! $role->isSuperAdmin()) {
            $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
            $role->update($data);
            $role->permissions()->sync($permissions);
        } else {
            $role->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->isSuperAdmin()) {
            return back()->withErrors(['role' => 'El rol Super admin no se puede eliminar.']);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'No puedes eliminar un rol asignado a usuarios.']);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Rol eliminado.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        $roleId = $role?->id;
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name')),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', Rule::unique('roles', 'slug')->ignore($roleId)],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);
    }
}
