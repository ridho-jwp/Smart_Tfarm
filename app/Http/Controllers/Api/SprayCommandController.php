<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SprayManualState;
use App\Models\DeteksiHama;

/**
 * SprayCommandController
 *
 * Endpoint ini di-polling oleh ESP32 setiap beberapa detik.
 * Response menentukan apakah solenoid kiri/kanan harus aktif.
 *
 * GET /api/v1/spray-command
 *
 * Response:
 * {
 *   "auto_mode":    true|false,
 *   "spray_kiri":   true|false,
 *   "spray_kanan":  true|false,
 *   "pump_on":      true|false,   // true jika salah satu solenoid aktif
 *   "source":       "auto"|"manual"
 * }
 */
class SprayCommandController extends Controller
{
    public function getCommand()
    {
        $state = SprayManualState::getState();

        if ($state->auto_mode) {
            // ── Mode Otomatis ─────────────────────────────────────
            // Baca data deteksi hama terbaru
            $latest = DeteksiHama::orderBy('created_at', 'desc')->first();

            $sprayKiri  = false;
            $sprayKanan = false;

            if ($latest && $latest->is_pestisida_pump && $latest->label_hama === 'hama') {
                $sprayKiri  = (bool) $latest->side_left;
                $sprayKanan = (bool) $latest->side_right;
            }

            return response()->json([
                'auto_mode'   => true,
                'spray_kiri'  => $sprayKiri,
                'spray_kanan' => $sprayKanan,
                'pump_on'     => $sprayKiri || $sprayKanan,
                'source'      => 'auto',
            ]);
        }

        // ── Mode Manual ───────────────────────────────────────────
        return response()->json([
            'auto_mode'   => false,
            'spray_kiri'  => $state->manual_kiri,
            'spray_kanan' => $state->manual_kanan,
            'pump_on'     => $state->manual_kiri || $state->manual_kanan,
            'source'      => 'manual',
        ]);
    }
}
