<?php

use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\DeviceStatusController;
use App\Http\Controllers\Api\AnomalyController;
use App\Http\Controllers\Api\PlantConfigApiController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Smart Pakcoy Hidroponik — API Routes |-------------------------------------------------------------------------- | Semua endpoint dilindungi oleh VerifyApiKey middleware. | Header yang diperlukan: X-API-Key: <token> */

Route::middleware('verify.api.key')->prefix('v1')->group(function () {

    // Terima data sensor dari ESP32
    Route::post('/sensor-data', [SensorDataController::class, 'store']);

    // Heartbeat dari ESP32
    Route::post('/heartbeat', [DeviceStatusController::class, 'heartbeat']);

    // ESP32 poll command terbaru
    Route::get('/command/{deviceId}', [DeviceStatusController::class, 'getCommand']);

    // Laporan anomali dari ESP32
    Route::post('/anomaly', [AnomalyController::class, 'store']);

    // Ambil konfigurasi batas optimal (ESP32 membaca min/max dari server)
    Route::get('/configs', [PlantConfigApiController::class, 'index']);
});
