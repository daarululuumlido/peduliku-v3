<?php

use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Wilayah Indonesia API (for lazy-loading address dropdowns)
Route::prefix('wilayah')->group(function () {
    Route::get('/provinsi', [WilayahController::class, 'provinces']);
    Route::get('/kota/{provinceId}', [WilayahController::class, 'cities']);
    Route::get('/kecamatan/{cityId}', [WilayahController::class, 'districts']);
    Route::get('/desa/{districtId}', [WilayahController::class, 'villages']);
    Route::get('/desa/search', [WilayahController::class, 'searchVillages']);
});
