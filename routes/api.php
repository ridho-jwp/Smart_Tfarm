<?php

use App\Http\Controllers\Api\NutrisiDosisController;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\DeviceStatusController;
use App\Http\Controllers\Api\AnomalyController;
use App\Http\Controllers\Api\DeteksiHamaController;
use App\Http\Controllers\Api\PlantConfigApiController;
use App\Http\Controllers\PestisidaController;
use App\Http\Controllers\Api\SprayCommandController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Smart Pakcoy Hidroponik — API Routes |-------------------------------------------------------------------------- | Semua endpoint dilindungi oleh VerifyApiKey middleware. | Header yang diperlukan: X-API-Key: <token> */

Route::prefix('v1')->group(function () {
    Route::post('/analisis', [DeteksiHamaController::class, 'ProsesAnalisis']);
});

Route::middleware('verify.api.key')->prefix('v1')->group(function () {

    // code abdul
    Route::get('/pump-status', [DeteksiHamaController::class, 'cekstatuspompa']);

    Route::post('/pestisida', [PestisidaController::class, 'mainPestisida']);



    // code ido
    // Terima data sensor dari ESP32
    Route::post('/sensor-data', [SensorDataController::class, 'store']);

    // Heartbeat dari ESP32
    Route::post('/heartbeat', [DeviceStatusController::class, 'heartbeat']);

    // ESP32 poll command terbaru
    Route::get('/command/{deviceId}', [DeviceStatusController::class, 'getCommand']);

    // Laporan anomali dari ESP32

    // Ambil konfigurasi batas optimal (ESP32 membaca min/max dari server)
    Route::get('/configs', [PlantConfigApiController::class, 'index']);


    // routes/api.php — tambahkan di dalam grup middleware X-API-Key

    Route::post('/nutrisi-dose', [NutrisiDosisController::class, 'dispatch']);
    Route::post('/nutrisi-dose/{id}/done', [NutrisiDosisController::class, 'done']);

    // Spray command — di-poll ESP32 setiap 5 detik
    // Response: auto_mode, spray_kiri, spray_kanan, pump_on, source
    Route::get('/spray-command', [SprayCommandController::class, 'getCommand']);
});