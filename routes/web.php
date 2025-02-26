<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


Route::middleware(['auth', 'role:admin'])->group(function () {
    // Roles Management
    Route::prefix('admin/roles')->name('admin.roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index'); // List roles
        Route::post('/', [RoleController::class, 'store'])->name('store'); // Create role
        Route::put('/{role}', [RoleController::class, 'update'])->name('update'); // Update role
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy'); // Delete role

        // ✅ New Routes for Assigning Permissions
        Route::get('/{role}/permissions', [RoleController::class, 'getPermissions'])->name('permissions'); // Get assigned permissions
        Route::put('/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('assign-permissions'); // Assign permissions
    });

    // Permissions Management
    Route::prefix('admin/permissions')->name('admin.permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index'); // List permissions
        Route::post('/', [PermissionController::class, 'store'])->name('store'); // Create permission
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update'); // Update permission
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy'); // Delete permission
    });
});


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index'); // List users
        Route::post('/', [UserController::class, 'store'])->name('store'); // Create user
        Route::get('/{user}', [UserController::class, 'show'])->name('show'); // View user
        Route::put('/{user}', [UserController::class, 'update'])->name('update'); // Update user
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy'); // Delete user
        Route::post('/{user}/assign-role', [UserController::class, 'assignRole'])->name('assignRole'); // Assign role
    });
});


//Route::get('/assign-role', function () {
//    $user = User::find(3); // Example user
//    $role = Role::findByName('admin');
//    $user->assignRole($role);
//    return "Role assigned!";
//});
