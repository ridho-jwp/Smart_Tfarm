<?php

namespace App\Http\Controllers;

use App\Models\ConfigPreset;
use App\Models\Pestisida;
use App\Models\PlantConfig;
use Illuminate\Http\Request;

class PestisidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $preset = ConfigPreset::find($id);

        if (!$preset) {
            return 'Gagal: Data dengan ID ' . $id . ' tidak ditemukan di tabel database.';
        }

        return view('configs.pestisida', compact('preset'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validasi = $request->validate([
            'id_preset' => 'required|exists:config_presets,id',
            'dosis' => 'required|numeric',
            'deskripsi' => 'nullable|string',
        ]);
        $pestisida = Pestisida::create([
            'id_preset' => $validasi['id_preset'],
            'dosis' => $validasi['dosis'],
            'deskripsi' => $validasi['deskripsi'] ?? null,
            'status' => 'pending',
        ]);
        return redirect()->route('configs.index')->with('success', 'Penambahan pestisida berhasil di buat', 201);
    }

    public function mainPestisida(Request $request)
    {
        $pestisida = Pestisida::where('status', 'pending')->latest()->first();

        $dosisDikeluarkan = 0;
        if ($pestisida) {
            $dosisDikeluarkan = $pestisida->dosis;

            $pestisida->update(['status' => 'dikirim']);
        }

        return response()->json([
            'status' => 'PUMP_ON',
            'dosis' => (float) $dosisDikeluarkan,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
