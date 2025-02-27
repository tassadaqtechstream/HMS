<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Role created successfully!');
    }

    public function update(Request $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id
        ]);

        $role->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role): \Illuminate\Http\RedirectResponse
    {
        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully!');
    }

    /**
     * Get assigned permissions for a specific role.
     */
    public function getPermissions(Role $role): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'role' => $role,
            'assigned_permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }),
            'permissions' => Permission::all(['id', 'name']), // Get all permissions with ID & name
        ]);
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissions(Request $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id' // Validate permission IDs
        ]);

        // Sync permissions (removes old and assigns new ones)
        $role->syncPermissions($request->permissions);

        // Reload roles with permissions for frontend
        return redirect()->back()->with('success', 'Permissions assigned successfully!');
//        return Inertia::render('Roles/Index')->with('flash', ['success' => 'Permissions assigned successfully!']);
    }

}
