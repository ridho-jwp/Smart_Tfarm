<?php

use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\DeviceStatusController;
use App\Http\Controllers\Api\AnomalyController;
use App\Http\Controllers\Api\DeteksiHamaController;
use App\Http\Controllers\Api\PlantConfigApiController;
use App\Http\Controllers\PestisidaController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Smart Pakcoy Hidroponik — API Routes |-------------------------------------------------------------------------- | Semua endpoint dilindungi oleh VerifyApiKey middleware. | Header yang diperlukan: X-API-Key: <token> */

Route::prefix('v1')->group(function () {
    Route::post('/analisis', [DeteksiHamaController::class, 'ProsesAnalisis']);
    Route::post('/pestisida', [PestisidaController::class, 'mainPestisida']);
});

Route::middleware('verify.api.key')->prefix('v1')->group(function () {
    // Route::post('/analisis', [DeteksiHamaController::class, 'ProsesAnalisis']);
    // Route::post('/pestisida',[PestisidaController::class,'mainPestisida'])->name('pestisida.main');

    // Terima data sensor dari ESP32
    Route::post('/sensor-data', [SensorDataController::class, 'store']);

    // Heartbeat dari ESP32
    Route::post('/heartbeat', [DeviceStatusController::class, 'heartbeat']);

    // ESP32 poll command terbaru
    Route::get('/command/{deviceId}', [DeviceStatusController::class, 'getCommand']);

    // Laporan anomali dari ESP32

    // Ambil konfigurasi batas optimal (ESP32 membaca min/max dari server)
    Route::get('/configs', [PlantConfigApiController::class, 'index']);


});