<?php

namespace App\Http\Controllers;

use App\Models\SprayManualState;
use Illuminate\Http\Request;

/**
 * SprayManualController
 *
 * Menangani toggle mode otomatis dan penyemprotan manual dari dashboard admin.
 */
class SprayManualController extends Controller
{
    /**
     * Toggle mode otomatis ON/OFF.
     * POST /spray/mode
     */
    public function toggleMode(Request $request)
    {
        $request->validate([
            'auto_mode' => 'required|boolean',
        ]);

        $state = SprayManualState::getState();
        $state->update([
            'auto_mode'  => $request->boolean('auto_mode'),
            'updated_by' => auth()->id(),
            // Saat beralih ke mode otomatis, reset state manual
            'manual_kiri'  => false,
            'manual_kanan' => false,
        ]);

        $label = $request->boolean('auto_mode') ? 'Otomatis' : 'Manual';
        return back()->with('success', "Mode penyemprotan diubah ke mode {$label}.");
    }

    /**
     * Toggle penyemprotan manual kiri/kanan.
     * POST /spray/manual
     */
    public function toggleManual(Request $request)
    {
        $request->validate([
            'target' => 'required|in:kiri,kanan,keduanya,off',
        ]);

        $state = SprayManualState::getState();

        // Pastikan mode manual aktif
        if ($state->auto_mode) {
            return back()->with('error', 'Nonaktifkan mode otomatis terlebih dahulu untuk kontrol manual.');
        }

        $target = $request->input('target');

        $updates = match ($target) {
            'kiri'     => ['manual_kiri' => true,  'manual_kanan' => false],
            'kanan'    => ['manual_kiri' => false,  'manual_kanan' => true],
            'keduanya' => ['manual_kiri' => true,   'manual_kanan' => true],
            'off'      => ['manual_kiri' => false,  'manual_kanan' => false],
        };

        $state->update(array_merge($updates, ['updated_by' => auth()->id()]));

        $labels = [
            'kiri'     => 'Semprot Kiri',
            'kanan'    => 'Semprot Kanan',
            'keduanya' => 'Semprot Kiri & Kanan',
            'off'      => 'Penyemprotan Dihentikan',
        ];

        return back()->with('success', $labels[$target] . ' berhasil diaktifkan.');
    }

    /**
     * Ambil state terkini sebagai JSON (untuk polling AJAX).
     * GET /spray/state
     */
    public function getState()
    {
        return response()->json(SprayManualState::getState());
    }
}
