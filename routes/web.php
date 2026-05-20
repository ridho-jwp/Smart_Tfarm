<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorHistoryController;
use App\Http\Controllers\DeviceControlController;
use App\Http\Controllers\DeteksiHamaController;
use App\Http\Controllers\PlantConfigController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PestisidaController;
use Illuminate\Support\Facades\Route;


// Landing page — halaman pertama yang dikunjungi
Route::get('/', [LandingController::class, 'index'])->name('landing');
// Route::get('/', function () {
//     return auth()->check() ? redirect('/dashboard') : redirect('/profile');
// });

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/profile', function () {
        return view('profile');
    });
});


// =============================================
// PROTECTED ROUTES (Auth required)
// =============================================
Route::middleware('auth')->group(function () {

    // Dashboard utama (termasuk kontrol pompa)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/sensor-data/latest', [DashboardController::class, 'latestSensorData'])->name('sensor.latest');

    // Kontrol pompa (dari dashboard)
    Route::post('/control/toggle', [DeviceControlController::class, 'toggle'])->name('control.toggle');

    // Deteksi anomali
    Route::get('/anomalies', [DeteksiHamaController::class, 'index'])->name('anomalies');

    // Riwayat sensor
    Route::get('/history', [SensorHistoryController::class, 'index'])->name('history');

    // Konfigurasi Ambang Batas
    Route::get('/configs', [PlantConfigController::class, 'index'])->name('configs.index');
    Route::post('/configs', [PlantConfigController::class, 'update'])->name('configs.update');


    // Preset Konfigurasi
    Route::post('/configs/presets/apply', [PlantConfigController::class, 'applyPreset'])->name('configs.preset.apply');
    Route::post('/configs/presets', [PlantConfigController::class, 'storePreset'])->name('configs.preset.store');
    Route::put('/configs/presets/{preset}', [PlantConfigController::class, 'updatePreset'])->name('configs.preset.update');
    Route::delete('/configs/presets/{preset}', [PlantConfigController::class, 'destroyPreset'])->name('configs.preset.destroy');

    // Pestisida Konfigurasi
    Route::get('/configs/pestisida/{id}', [PestisidaController::class, 'index'])->name('configs.pestisida');
    Route::post('/configs/pestisida/{id}', [PestisidaController::class, 'create'])->name('pestisida.store');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

});