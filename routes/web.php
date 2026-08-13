<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'showSignin'])->name('login');
    Route::post('/signin', [AuthController::class, 'signin']);

    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup']);
});

use App\Modules\Users\Controllers\UserController;
use App\Modules\Products\Controllers\ProductController;
use App\Modules\RolePermission\Controllers\RolePermissionController;

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Users Management Routes (Separated & Prefixed)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Products Routes (Prefixed)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Role & Permissions Settings Routes (Prefixed)
    Route::prefix('settings')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('role-permissions.index');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('role-permissions.roles.store');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('role-permissions.roles.destroy');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('role-permissions.permissions.store');
        Route::post('/update', [RolePermissionController::class, 'updatePermissions'])->name('role-permissions.update');
    });
});


