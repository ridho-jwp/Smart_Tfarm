<?php

namespace App\Http\Controllers;

use App\Models\DeteksiHama;
use Illuminate\Http\Request;

class DeteksiHamaController extends Controller
{
    /**
     * Daftar anomali yang terdeteksi (dengan filter).
     */

    public function index(Request $request)
    {
        $query = DeteksiHama::query();

        // Filter status
        if ($request->filter === 'resolved') {
            $query->where('is_pestisida_pump', 0);
        } elseif ($request->filter === 'unresolved') {
            $query->where('is_pestisida_pump', 1);
        }

        $data = $query
            ->latest()
            ->paginate(12);

        return view('anomalies', compact('data'));
    }
    /**
     * Tandai anomali sebagai resolved.
     */
    // public function resolve(Request $request, Anomaly $anomaly)
    // {
    //     $anomaly->update(['resolved_at' => now()]);

    //     return redirect()->back()->with('success', 'Anomali berhasil ditandai sebagai selesai.');
    // }
}