<?php
// app/Http/Controllers/Api/NutrisiDoseApiController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\NutrisiDose;
use App\Models\NutrisiDosis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutrisiDosisController extends Controller
{
    /**
     * ESP32 poll: ambil 1 dosis nutrisi pending tertua.
     *
     * POST /api/v1/nutrisi-dose
     * Headers: X-API-Key: <token>
     *
     * Response (ada antrean):
     * {
     *   "has_dose": true,
     *   "dose_id": 42,
     *   "dosis_ml": 2.69,
     *   "durasi_detik": 2.69,
     *   "ppm_deficit": 188.0,
     *   "ppm_target": 400.0
     * }
     *
     * Response (tidak ada):
     * { "has_dose": false }
     */
    public function dispatch(Request $request): JsonResponse
    {
        // Cari 1 dosis pending paling lama
        $dose = NutrisiDosis::where('status', 'pending')
            ->orderBy('created_at')
            ->lockForUpdate()   // hindari race condition
            ->first();

        if (!$dose) {
            return response()->json(['has_dose' => false]);
        }

        // Tandai sebagai dispatched agar tidak diambil dua kali
        $dose->update([
            'status' => 'dispatched',
            'dispatched_at' => now(),
        ]);

        return response()->json([
            'has_dose' => true,
            'dose_id' => $dose->id,
            'dosis_ml' => (float) $dose->dosis_ml,
            'durasi_detik' => (float) $dose->durasi_detik,
            'ppm_deficit' => (float) $dose->ppm_deficit,
            'ppm_target' => (float) $dose->ppm_target,
        ]);
    }

    /**
     * ESP32 konfirmasi bahwa pompa sudah selesai berjalan.
     *
     * POST /api/v1/nutrisi-dose/{id}/done
     * Headers: X-API-Key: <token>
     */
    public function done(Request $request, int $id): JsonResponse
    {
        $dose = NutrisiDosis::findOrFail($id);

        if ($dose->status === 'dispatched') {
            $dose->update([
                'status' => 'done',
                'done_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}