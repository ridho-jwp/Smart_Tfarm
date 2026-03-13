<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlantConfig;

class PlantConfigApiController extends Controller
{
    /**
     * Kembalikan semua konfigurasi batas optimal untuk ESP32.
     *
     * GET /api/v1/configs
     * Headers: X-API-Key: <token>
     */
    public function index()
    {
        $configs = PlantConfig::all()->keyBy('parameter')->map(function ($config) {
            return [
                'parameter' => $config->parameter,
                'label' => $config->label,
                'min_optimal' => (float) $config->min_optimal,
                'max_optimal' => (float) $config->max_optimal,
                'unit' => $config->unit,
            ];
        });

        return response()->json([
            'success' => true,
            'configs' => $configs,
        ]);
    }
}
