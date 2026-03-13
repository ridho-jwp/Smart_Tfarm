<?php

namespace App\Http\Controllers;

use App\Models\Anomaly;
use Illuminate\Http\Request;

class AnomalyController extends Controller
{
    /**
     * Daftar anomali yang terdeteksi (dengan filter).
     */
    public function index(Request $request)
    {
        $query = Anomaly::with('device')->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        $filter = $request->input('filter', 'all');
        if ($filter === 'unresolved') {
            $query->whereNull('resolved_at');
        }
        elseif ($filter === 'resolved') {
            $query->whereNotNull('resolved_at');
        }

        $anomalies = $query->paginate(12);

        return view('anomalies', compact('anomalies'));
    }

    /**
     * Tandai anomali sebagai resolved.
     */
    public function resolve(Request $request, Anomaly $anomaly)
    {
        $anomaly->update(['resolved_at' => now()]);

        return redirect()->back()->with('success', 'Anomali berhasil ditandai sebagai selesai.');
    }
}
