<?php

use App\Http\Controllers\ProfileController;
use App\Services\ModuleService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Default dashboard redirect to first accessible module
Route::get('/dashboard', function (ModuleService $moduleService) {
    $user = request()->user();
    $defaultModule = $moduleService->getDefaultModule($user);
    
    if ($defaultModule) {
        return redirect()->route("{$defaultModule}.dashboard");
    }
    
    // Fallback if no modules accessible
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Module Routes
|--------------------------------------------------------------------------
*/


require __DIR__.'/modules/admin.php';
require __DIR__.'/auth.php';
