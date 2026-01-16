<?php

use App\Http\Controllers\StrukturOrganisasiController;
use App\Http\Controllers\UnitOrganisasiController;
use App\Http\Controllers\PeranPegawaiController;
use App\Http\Controllers\RiwayatPendidikanController;
use App\Http\Controllers\RiwayatKeluargaController;
use App\Http\Controllers\RiwayatIbadahController;
use App\Http\Controllers\CatatanKepegawaianController;
use App\Http\Controllers\DokumenPegawaiController;
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
        Route::get('/create', [StrukturOrganisasiController::class, 'create'])->name('create');
        Route::post('/', [StrukturOrganisasiController::class, 'store'])->name('store');
        Route::get('/{strukturOrganisasi}', [StrukturOrganisasiController::class, 'show'])->name('show');
        Route::get('/{strukturOrganisasi}/edit', [StrukturOrganisasiController::class, 'edit'])->name('edit');
        Route::put('/{strukturOrganisasi}', [StrukturOrganisasiController::class, 'update'])->name('update');
        Route::delete('/{strukturOrganisasi}', [StrukturOrganisasiController::class, 'destroy'])->name('destroy');
        Route::post('/{strukturOrganisasi}/clone', [StrukturOrganisasiController::class, 'clone'])->name('clone');
    });

    // Unit Organisasi Routes
    Route::prefix('unit-organisasi')->name('unit-organisasi.')->group(function () {
        Route::get('/', [UnitOrganisasiController::class, 'index'])->name('index');
        Route::post('/', [UnitOrganisasiController::class, 'store'])->name('store');
        Route::get('/{unitOrganisasi}', [UnitOrganisasiController::class, 'show'])->name('show');
        Route::get('/{unitOrganisasi}/edit', [UnitOrganisasiController::class, 'edit'])->name('edit');
        Route::put('/{unitOrganisasi}', [UnitOrganisasiController::class, 'update'])->name('update');
        Route::delete('/{unitOrganisasi}', [UnitOrganisasiController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [UnitOrganisasiController::class, 'reorder'])->name('reorder');
    });

    // Peran Pegawai Routes
    Route::prefix('pegawai')->name('pegawai.')->group(function () {
        Route::get('/', [PeranPegawaiController::class, 'index'])->name('index');
        Route::get('/create', [PeranPegawaiController::class, 'create'])->name('create');
        Route::post('/', [PeranPegawaiController::class, 'store'])->name('store');
        Route::get('/search-orang', [PeranPegawaiController::class, 'searchOrang'])->name('search-orang');
        Route::get('/{peranPegawai}', [PeranPegawaiController::class, 'show'])->name('show');
        Route::get('/{peranPegawai}/edit', [PeranPegawaiController::class, 'edit'])->name('edit');
        Route::get('/{peranPegawai}/current-jabatan', [PeranPegawaiController::class, 'currentJabatan'])->name('current-jabatan');
        Route::put('/{peranPegawai}', [PeranPegawaiController::class, 'update'])->name('update');
        Route::delete('/{peranPegawai}', [PeranPegawaiController::class, 'destroy'])->name('destroy');
        Route::post('/{peranPegawai}/assign-jabatan', [PeranPegawaiController::class, 'assignJabatan'])->name('assign-jabatan');
    });

    // MODUL 2.3: Riwayat & Catatan Khusus Routes
    Route::prefix('pegawai/{pegawai}')->group(function () {
        // Riwayat Pendidikan
        Route::prefix('pendidikan')->name('pendidikan.')->group(function () {
            Route::get('/', [RiwayatPendidikanController::class, 'index'])->name('index');
            Route::post('/', [RiwayatPendidikanController::class, 'store'])->name('store');
            Route::put('/{riwayatPendidikan}', [RiwayatPendidikanController::class, 'update'])->name('update');
            Route::delete('/{riwayatPendidikan}', [RiwayatPendidikanController::class, 'destroy'])->name('destroy');
        });

        // Riwayat Keluarga
        Route::prefix('keluarga')->name('keluarga.')->group(function () {
            Route::get('/', [RiwayatKeluargaController::class, 'index'])->name('index');
            Route::post('/', [RiwayatKeluargaController::class, 'store'])->name('store');
            Route::put('/{riwayatKeluarga}', [RiwayatKeluargaController::class, 'update'])->name('update');
            Route::delete('/{riwayatKeluarga}', [RiwayatKeluargaController::class, 'destroy'])->name('destroy');
        });

        // Riwayat Ibadah
        Route::prefix('ibadah')->name('ibadah.')->group(function () {
            Route::get('/', [RiwayatIbadahController::class, 'index'])->name('index');
            Route::post('/', [RiwayatIbadahController::class, 'store'])->name('store');
            Route::put('/{riwayatIbadah}', [RiwayatIbadahController::class, 'update'])->name('update');
            Route::delete('/{riwayatIbadah}', [RiwayatIbadahController::class, 'destroy'])->name('destroy');
        });

        // Catatan Kepegawaian
        Route::prefix('catatan')->name('catatan.')->group(function () {
            Route::get('/', [CatatanKepegawaianController::class, 'index'])->name('index');
            Route::post('/', [CatatanKepegawaianController::class, 'store'])->name('store');
            Route::put('/{catatan}', [CatatanKepegawaianController::class, 'update'])->name('update');
            Route::delete('/{catatan}', [CatatanKepegawaianController::class, 'destroy'])->name('destroy');
        });
    });

    // MODUL 2.4: Dokumen Routes
    Route::prefix('pegawai/{pegawai}/dokumen')->name('dokumen.')->group(function () {
        Route::get('/', [DokumenPegawaiController::class, 'index'])->name('index');
        Route::post('/', [DokumenPegawaiController::class, 'store'])->name('store');
        Route::put('/{dokumen}', [DokumenPegawaiController::class, 'update'])->name('update');
        Route::post('/{dokumen}/verify', [DokumenPegawaiController::class, 'verify'])->name('verify');
        Route::delete('/{dokumen}', [DokumenPegawaiController::class, 'destroy'])->name('destroy');
    });
});
