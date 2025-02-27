<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return Inertia::render('Permissions/Index', [
            'permissions' => Permission::all(),
            'flash' => session('success') // Pass flash messages to frontend
        ]);
    }


    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Permission created successfully!');
    }

    public function update(Request $request, Permission $permission): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Permission updated successfully!');
    }

    public function destroy(Permission $permission): \Illuminate\Http\RedirectResponse
    {
        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully!');
    }
}
