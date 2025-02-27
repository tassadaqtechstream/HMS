<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::with('roles')->get(),
            'roles' => Role::all(),
        ]);
    }


    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Validate Request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,id',
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Fill form correct!');
        }
        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign Role
        $user->assignRole($request->role);

        return redirect()->back()->with('success', 'User created and role assigned successfully!');
    }



    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        // Manually validate
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|min:6', // Nullable so it's not required every time
            'role' => 'required|exists:roles,id',
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Fill form correct!');
        }
        // Update User Information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);


        // Remove Previous Roles and Assign New Role
        $user->syncRoles($request->role);

        return redirect()->back()->with('success', 'User and role updated successfully!');

    }

    public function destroy(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }

}
