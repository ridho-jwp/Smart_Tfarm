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
        $latest = DeteksiHama::latest()->first();

        if (!$latest) {
            return view('anomalies', ['data' => collect()]);
        }

        $data = DeteksiHama::where('session_id', $latest->session_id)->orderBy('created_at', 'desc')->get();

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