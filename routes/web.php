<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorHistoryController;
use App\Http\Controllers\DeviceControlController;
use App\Http\Controllers\AnomalyController;
use App\Http\Controllers\PlantConfigController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Smart Pakcoy Hidroponik — Web Routes |-------------------------------------------------------------------------- */

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// =============================================
// AUTH ROUTES (Guest only)
// =============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

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
    Route::get('/anomalies', [AnomalyController::class, 'index'])->name('anomalies');
    Route::post('/anomalies/{anomaly}/resolve', [AnomalyController::class, 'resolve'])->name('anomalies.resolve');

    // Riwayat sensor
    Route::get('/history', [SensorHistoryController::class, 'index'])->name('history');

    // Konfigurasi Ambang Batas
    Route::get('/configs', [PlantConfigController::class, 'index'])->name('configs.index');
    Route::post('/configs', [PlantConfigController::class, 'update'])->name('configs.update');
});
