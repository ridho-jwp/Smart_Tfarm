<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Daftar semua perangkat dan statusnya.
     */
    public function index()
    {
        $devices = Device::withCount(['sensorData', 'anomalies', 'logs'])
            ->orderBy('name')
            ->get();

        return view('devices', compact('devices'));
    }
}
