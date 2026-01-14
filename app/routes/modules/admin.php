<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\KartuKeluargaController;
use App\Http\Controllers\OrangController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'module:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Orang (People) Management Routes
    Route::get('/orang/trashed', [OrangController::class, 'trashed'])->name('orang.trashed');
    Route::post('/orang/{id}/restore', [OrangController::class, 'restore'])->name('orang.restore');
    Route::delete('/orang/{id}/force-delete', [OrangController::class, 'forceDelete'])->name('orang.force-delete');
    Route::resource('orang', OrangController::class);

    // Alamat Management Routes
    Route::get('alamat/search', [\App\Http\Controllers\AlamatController::class, 'search'])->name('alamat.search');
    Route::resource('alamat', \App\Http\Controllers\AlamatController::class);

    // Kartu Keluarga Routes
    Route::get('kartu-keluarga/search', [KartuKeluargaController::class, 'search'])->name('kartu-keluarga.search');
    Route::post('/kartu-keluarga/{kartuKeluarga}/add-member', [KartuKeluargaController::class, 'addMember'])->name('kartu-keluarga.add-member');
    Route::post('/kartu-keluarga/{kartuKeluarga}/remove-member', [KartuKeluargaController::class, 'removeMember'])->name('kartu-keluarga.remove-member');
    Route::resource('kartu-keluarga', KartuKeluargaController::class);

    // Settings Routes (Roles, Permissions, Users)
    Route::prefix('settings')->name('settings.')->group(function () {
        // Roles Management
        Route::resource('roles', RoleController::class)->except(['show']);

        // Permissions Management
        Route::resource('permissions', PermissionController::class)->except(['show']);

        // Users Management
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserController::class)->except(['show']);
    });
});
