@extends('layouts.app')
@section('title', 'Riwayat Sensor — Smart Pakcoy')

@section('content')

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Riwayat Sensor</h6>
            <p class="text-xs text-secondary-light mt-4 mb-0">Rekap data sensor IoT setiap 5 menit</p>
        </div>
        <form method="GET" action="{{ route('history') }}" class="d-flex align-items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm w-auto radius-8">
            <button type="submit" class="btn btn-primary btn-sm radius-8 px-16">
                <iconify-icon icon="solar:calendar-outline" class="me-1"></iconify-icon>
                Tampilkan
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @forelse($grouped as $row)
                <div class="d-flex flex-wrap align-items-start gap-4 p-20 {{ !$loop->last ? 'border-bottom' : '' }}">
                    {{-- Time --}}
                    <div class="d-flex align-items-center gap-8 flex-shrink-0" style="width:100px">
                        <div
                            class="w-36-px h-36-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:clock-circle-outline" class="text-secondary-light"></iconify-icon>
                        </div>
                        <div>
                            <p class="fw-semibold text-sm mb-0 font-monospace">{{ $row['waktu'] }}</p>
                            <p class="text-xs text-secondary-light mb-0">5 menit</p>
                        </div>
                    </div>

                    {{-- Sensor Data --}}
                    <div class="flex-grow-1">
                        <div class="row row-cols-3 row-cols-sm-6 gy-3">
                            <div class="col">
                                <div class="bg-info-focus rounded-8 p-12 text-center">
                                    <p class="text-xs text-info-main fw-semibold mb-4">pH</p>
                                    <p class="fw-bold text-sm mb-0">{{ $row['ph_avg'] }}</p>
                                    <p class="text-xs text-secondary-light mb-0">{{ $row['ph_min'] }}–{{ $row['ph_max'] }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="bg-warning-focus rounded-8 p-12 text-center">
                                    <p class="text-xs text-warning-main fw-semibold mb-4">Suhu</p>
                                    <p class="fw-bold text-sm mb-0">{{ $row['suhu_avg'] }}°C</p>
                                    <p class="text-xs text-secondary-light mb-0">{{ $row['suhu_min'] }}–{{ $row['suhu_max'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="bg-primary-50 rounded-8 p-12 text-center">
                                    <p class="text-xs text-primary-600 fw-semibold mb-4">PPM</p>
                                    <p class="fw-bold text-sm mb-0">{{ $row['ppm_avg'] }}</p>
                                    <p class="text-xs text-secondary-light mb-0">{{ $row['ppm_min'] }}–{{ $row['ppm_max'] }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="bg-yellow-light rounded-8 p-12 text-center">
                                    <p class="text-xs text-yellow fw-semibold mb-4">Volt</p>
                                    <p class="fw-bold text-sm mb-0">{{ $row['voltage_avg'] ?? '--' }} V</p>
                                    <p class="text-xs text-secondary-light mb-0">
                                        {{ $row['voltage_min'] ?? '--' }}–{{ $row['voltage_max'] ?? '--' }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="bg-danger-focus rounded-8 p-12 text-center">
                                    <p class="text-xs text-danger-main fw-semibold mb-4">Watt</p>
                                    <p class="fw-bold text-sm mb-0">{{ $row['power_avg'] ?? '--' }} W</p>
                                    <p class="text-xs text-secondary-light mb-0">
                                        {{ $row['power_min'] ?? '--' }}–{{ $row['power_max'] ?? '--' }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="bg-success-focus rounded-8 p-12 text-center">
                                    <p class="text-xs text-success-main fw-semibold mb-4">kWh</p>
                                    <p class="fw-bold text-sm mb-0">{{ $row['energy_avg'] ?? '--' }}</p>
                                    <p class="text-xs text-secondary-light mb-0">Rata-rata</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="flex-shrink-0 d-flex align-items-center">
                        @if(isset($row['has_anomaly']) && $row['has_anomaly'])
                            <a href="{{ route('anomalies') }}"
                                class="badge bg-danger-focus text-danger-main rounded-pill px-12 py-6 text-xs d-flex align-items-center gap-1 text-decoration-none">
                                <iconify-icon icon="mdi:bug" class="text-sm"></iconify-icon>
                                Hama
                            </a>
                        @else
                            <span
                                class="badge bg-success-focus text-success-main rounded-pill px-12 py-6 text-xs d-flex align-items-center gap-1">
                                <iconify-icon icon="mdi:check-circle" class="text-sm"></iconify-icon>
                                Normal
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-secondary-light w-100 mb-0 mt-4">{{ $row['jumlah_data'] }} pembacaan sensor</p>
                </div>
            @empty
                <div class="text-center py-48">
                    <iconify-icon icon="mdi:database-off-outline" class="text-neutral-300 display-4"></iconify-icon>
                    <p class="text-secondary-light mt-16 mb-0">Tidak ada data sensor untuk tanggal ini.</p>
                </div>
            @endforelse
        </div>
    </div>

@endsection