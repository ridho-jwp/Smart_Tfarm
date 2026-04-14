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
        $data = DeteksiHama::all()->sortByDesc('created_at');
        return view('anomalies',compact('data'));
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
