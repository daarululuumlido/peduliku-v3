<?php

use App\Http\Controllers\StrukturOrganisasiController;
use App\Http\Controllers\UnitOrganisasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HRIS Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('hris')->name('hris.')->middleware(['auth'])->group(function () {
    // Struktur Organisasi Routes
    Route::prefix('struktur-organisasi')->name('struktur-organisasi.')->group(function () {
        Route::get('/', [StrukturOrganisasiController::class, 'index'])->name('index');
        Route::post('/', [StrukturOrganisasiController::class, 'store'])->name('store');
        Route::get('/{strukturOrganisasi}', [StrukturOrganisasiController::class, 'show'])->name('show');
        Route::put('/{strukturOrganisasi}', [StrukturOrganisasiController::class, 'update'])->name('update');
        Route::delete('/{strukturOrganisasi}', [StrukturOrganisasiController::class, 'destroy'])->name('destroy');
        Route::post('/{strukturOrganisasi}/clone', [StrukturOrganisasiController::class, 'clone'])->name('clone');
    });

    // Unit Organisasi Routes
    Route::prefix('unit-organisasi')->name('unit-organisasi.')->group(function () {
        Route::get('/', [UnitOrganisasiController::class, 'index'])->name('index');
        Route::post('/', [UnitOrganisasiController::class, 'store'])->name('store');
        Route::get('/{unitOrganisasi}', [UnitOrganisasiController::class, 'show'])->name('show');
        Route::put('/{unitOrganisasi}', [UnitOrganisasiController::class, 'update'])->name('update');
        Route::delete('/{unitOrganisasi}', [UnitOrganisasiController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [UnitOrganisasiController::class, 'reorder'])->name('reorder');
    });
});
