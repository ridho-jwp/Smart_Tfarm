@extends('layouts.app')
@section('title', 'Dashboard — Smart Pakcoy Hidroponik')

@section('content')

    {{-- Page header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Dashboard</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Monitoring Real-time</li>
        </ul>
    </div>

    {{-- ─── Sensor Cards ─── --}}
    @php
        $phMin    = $configs['ph']->min_optimal          ?? 5.5;
        $phMax    = $configs['ph']->max_optimal          ?? 6.5;
        $suhuMin  = $configs['suhu']->min_optimal        ?? 22;
        $suhuMax  = $configs['suhu']->max_optimal        ?? 30;
        $ppmMin   = $configs['ppm']->min_optimal         ?? 500;
        $ppmMax   = $configs['ppm']->max_optimal         ?? 1200;
        $airNyala = $configs['ketinggian_air']->max_optimal ?? 30; // jarak besar = air rendah

        $ph          = $latestSensor?->ph;
        $suhu        = $latestSensor?->suhu;
        $ppm         = $latestSensor?->ppm;
        $water_level = $latestSensor?->water_level; // jarak sensor ke permukaan air (cm)

        $phStatus    = $ph !== null   && $ph   >= $phMin   && $ph   <= $phMax;
        $suhuStatus  = $suhu !== null && $suhu >= $suhuMin && $suhu <= $suhuMax;
        $ppmStatus   = $ppm !== null  && $ppm  >= $ppmMin  && $ppm  <= $ppmMax;
        // Air OK jika jarak KECIL (air dekat sensor = penuh) — rendah jika jarak >= airNyala
        $airStatus   = $water_level !== null && $water_level < $airNyala;
    @endphp

    <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4 mb-24">

        {{-- pH Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-1 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">pH Air</p>
                            <h4 class="mb-0 fw-bold" id="ph-value">{{ $ph ?? '--' }}</h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0" id="ph-optimal">Optimal: {{ $phMin }} – {{ $phMax }}</p>
                        </div>
                        <div class="w-44-px h-44-px bg-cyan-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="fluent:drop-20-filled" class="text-cyan text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    @if($latestSensor)
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                            <span id="ph-badge" class="d-inline-flex align-items-center gap-1 {{ $phStatus ? 'text-success-main' : 'text-danger-main' }}">
                                <iconify-icon icon="{{ $phStatus ? 'bxs:up-arrow' : 'bxs:down-arrow' }}" class="text-xs"></iconify-icon>
                                {{ $phStatus ? 'Normal' : 'Perhatian' }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Suhu Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Suhu Air</p>
                            <h4 class="mb-0 fw-bold" id="suhu-value">{{ $suhu ? $suhu . '°C' : '--' }}</h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0" id="suhu-optimal">Optimal: {{ $suhuMin }} – {{ $suhuMax }}°C</p>
                        </div>
                        <div class="w-44-px h-44-px bg-purple-light rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:thermometer" class="text-purple text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    @if($latestSensor)
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                            <span id="suhu-badge" class="d-inline-flex align-items-center gap-1 {{ $suhuStatus ? 'text-success-main' : 'text-danger-main' }}">
                                <iconify-icon icon="{{ $suhuStatus ? 'bxs:up-arrow' : 'bxs:down-arrow' }}" class="text-xs"></iconify-icon>
                                {{ $suhuStatus ? 'Normal' : 'Perhatian' }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- PPM / TDS Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-3 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Nutrisi (TDS)</p>
                            <h4 class="mb-0 fw-bold" id="ppm-value">{{ $ppm ? $ppm . ' ppm' : '--' }}</h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0" id="ppm-optimal">Optimal: {{ $ppmMin }} – {{ $ppmMax }} ppm</p>
                        </div>
                        <div class="w-44-px h-44-px bg-info-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:flask" class="text-info-main text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    @if($latestSensor)
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                            <span id="ppm-badge" class="d-inline-flex align-items-center gap-1 {{ $ppmStatus ? 'text-success-main' : 'text-danger-main' }}">
                                <iconify-icon icon="{{ $ppmStatus ? 'bxs:up-arrow' : 'bxs:down-arrow' }}" class="text-xs"></iconify-icon>
                                {{ $ppmStatus ? 'Normal' : ($ppm < $ppmMin ? 'Rendah — Pompa Aktif' : 'Terlalu Tinggi') }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ketinggian Air Tandon --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Air Tandon</p>
                            <h4 class="mb-0 fw-bold" id="air-value">
                                {{ $water_level !== null ? $water_level . ' cm' : '--' }}
                            </h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0">Jarak sensor ke air</p>
                        </div>
                        <div class="w-44-px h-44-px bg-success-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:water" class="text-success-main text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    @if($latestSensor)
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                            <span id="air-badge" class="d-inline-flex align-items-center gap-1 {{ $airStatus ? 'text-success-main' : 'text-danger-main' }}">
                                <iconify-icon icon="{{ $airStatus ? 'bxs:up-arrow' : 'bxs:down-arrow' }}" class="text-xs"></iconify-icon>
                                {{ $airStatus ? 'Aman' : 'RENDAH! Isi Air' }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Voltage Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-5 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Tegangan PLN</p>
                            <h4 class="mb-0 fw-bold" id="voltage-value">
                                {{ $latestSensor?->voltage ? $latestSensor->voltage . ' V' : '--' }}
                            </h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0">PZEM-004T</p>
                        </div>
                        <div class="w-44-px h-44-px bg-warning-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:lightning-bolt" class="text-warning-main text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Grafik Sensor ─── --}}
    <div class="mb-24">
        <div class="d-flex align-items-center gap-2 mb-16">
            <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:graph-new-up-outline" class="text-primary"></iconify-icon>
                Grafik Sensor (24 Jam)
            </h6>
            <span class="text-xs text-secondary-light" id="last-update">Update otomatis setiap 1 menit</span>
        </div>
        <div class="row gy-4">
            <div class="col-xxl-6 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="fluent:drop-20-filled" class="text-cyan"></iconify-icon>
                                Grafik pH
                            </h6>
                            <span class="text-sm fw-semibold rounded-pill bg-info-focus text-info-main border br-info px-8 py-4">pH Air</span>
                        </div>
                        <div id="phChart" style="height:220px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="mdi:thermometer" class="text-purple"></iconify-icon>
                                Grafik Suhu
                            </h6>
                            <span class="text-sm fw-semibold rounded-pill bg-purple-light text-purple border br-purple px-8 py-4">°C</span>
                        </div>
                        <div id="suhuChart" style="height:220px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="mdi:flask" class="text-success-main"></iconify-icon>
                                Grafik PPM/TDS Nutrisi
                            </h6>
                            <span class="text-sm fw-semibold rounded-pill bg-success-focus text-success-main border br-success px-8 py-4">ppm</span>
                        </div>
                        <div id="ppmChart" style="height:220px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="mdi:water" class="text-info-main"></iconify-icon>
                                Grafik Ketinggian Air Tandon
                            </h6>
                            <span class="text-sm fw-semibold rounded-pill bg-info-focus text-info-main border br-info px-8 py-4">cm</span>
                        </div>
                        <div id="airChart" style="height:220px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Kontrol Pompa ─── --}}
    <div class="row gy-4 mb-24">

        {{-- Mini Waterpump Sirkulasi --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-20">
                        <div class="w-48-px h-48-px bg-primary-light text-primary rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="mdi:water-pump" class="text-xl"></iconify-icon>
                        </div>
                        <div class="flex-1">
                            <h6 class="fw-semibold mb-0">Mini Waterpump (Sirkulasi)</h6>
                            <p class="text-xs text-secondary-light mb-0">Kontrol sirkulasi air hidroponik</p>
                        </div>
                        @if($circPump)
                            <span class="badge {{ $circPump->is_online ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} rounded-pill px-12 py-4 text-sm">
                                {{ $circPump->is_online ? 'Online' : 'Offline' }}
                            </span>
                        @endif
                    </div>

                    @if($circPump)
                        {{-- Status badge --}}
                        <div class="mb-16">
                            <span class="badge {{ $circIsOn ? 'bg-success-focus text-success-main' : 'bg-secondary text-white' }} px-12 py-6 text-sm rounded-pill">
                                <iconify-icon icon="{{ $circIsOn ? 'mdi:lightning-bolt' : 'mdi:power-off' }}" class="me-1"></iconify-icon>
                                Status: {{ $circIsOn ? 'NYALA' : 'MATI' }}
                            </span>
                        </div>
                        <div class="d-flex gap-12 mb-20">
                            <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="device_id" value="{{ $circPump->id }}">
                                <input type="hidden" name="action" value="circulation_on">
                                <button type="submit" class="btn btn-outline-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="mdi:lightning-bolt" class="text-lg"></iconify-icon> ON
                                </button>
                            </form>
                            <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="device_id" value="{{ $circPump->id }}">
                                <input type="hidden" name="action" value="circulation_off">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="mdi:power-off" class="text-lg"></iconify-icon> OFF
                                </button>
                            </form>
                        </div>
                        <div class="border-top pt-16">
                            <h6 class="text-xs fw-semibold text-secondary-light text-uppercase mb-12">Riwayat Nyala/Mati</h6>
                            <div class="max-h-200-px overflow-y-auto scroll-sm">
                                @forelse($circLogs as $log)
                                    <div class="d-flex align-items-center justify-content-between py-8 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex align-items-center gap-8">
                                            <span class="w-8-px h-8-px rounded-circle {{ $log->action === 'circulation_on' ? 'bg-success-main' : 'bg-danger-main' }}"></span>
                                            <span class="text-sm text-secondary-light">{{ $log->action === 'circulation_on' ? 'Nyala' : 'Mati' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-8 text-xs text-secondary-light">
                                            <span>{{ $log->user?->name ?? 'Sistem' }}</span>
                                            <span>{{ $log->created_at->format('d/m H:i') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-secondary-light text-center py-12 mb-0">Belum ada riwayat.</p>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="text-center py-24">
                            <iconify-icon icon="mdi:water-pump-off" class="text-4xl text-secondary-light mb-8"></iconify-icon>
                            <p class="text-sm text-secondary-light mb-4">Pompa sirkulasi belum terdaftar.</p>
                            <p class="text-xs text-secondary-light mb-0">Device ID: <code>ESP32-PUMP-SIRKULASI</code></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pompa Peristaltik (Nutrisi Otomatis) --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-20">
                        <div class="w-48-px h-48-px bg-warning-focus text-warning-main rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="mdi:beaker-outline" class="text-xl"></iconify-icon>
                        </div>
                        <div class="flex-1">
                            <h6 class="fw-semibold mb-0">Pompa Peristaltik (Nutrisi)</h6>
                            <p class="text-xs text-secondary-light mb-0">Otomatis: nyala 60 detik jika PPM rendah</p>
                        </div>
                        @if($periPump)
                            <span class="badge {{ $periPump->is_online ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} rounded-pill px-12 py-4 text-sm">
                                {{ $periPump->is_online ? 'Online' : 'Offline' }}
                            </span>
                        @endif
                    </div>

                    @if($periPump)
                        {{-- Status badge --}}
                        <div class="mb-16">
                            <span class="badge {{ $periIsOn ? 'bg-warning-focus text-warning-main' : 'bg-secondary text-white' }} px-12 py-6 text-sm rounded-pill">
                                <iconify-icon icon="{{ $periIsOn ? 'mdi:pump' : 'mdi:pump-off' }}" class="me-1"></iconify-icon>
                                Status: {{ $periIsOn ? 'AKTIF (Dosing Nutrisi)' : 'STANDBY' }}
                            </span>
                        </div>
                        <div class="alert {{ $ppmStatus ? 'alert-success' : 'alert-warning' }} py-8 px-12 mb-16" role="alert" style="font-size:13px;">
                            <iconify-icon icon="{{ $ppmStatus ? 'mdi:check-circle' : 'mdi:alert' }}" class="me-1"></iconify-icon>
                            @if($ppmStatus)
                                Nutrisi dalam kondisi optimal ({{ $ppm ?? '--' }} ppm).
                            @else
                                Nutrisi {{ $ppm !== null && $ppm < $ppmMin ? 'rendah' : 'tidak normal' }} ({{ $ppm ?? '--' }} ppm). Pompa otomatis aktif.
                            @endif
                        </div>
                        <div class="border-top pt-16">
                            <h6 class="text-xs fw-semibold text-secondary-light text-uppercase mb-12">Riwayat Dosing</h6>
                            <div class="max-h-200-px overflow-y-auto scroll-sm">
                                @forelse($periLogs as $log)
                                    <div class="d-flex align-items-center justify-content-between py-8 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex align-items-center gap-8">
                                            <span class="w-8-px h-8-px rounded-circle {{ $log->action === 'peristaltic_on' ? 'bg-warning-main' : 'bg-secondary' }}"></span>
                                            <span class="text-sm text-secondary-light">{{ $log->action === 'peristaltic_on' ? 'Dosing Mulai' : 'Dosing Selesai' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-8 text-xs text-secondary-light">
                                            <span>{{ $log->user?->name ?? 'Otomatis' }}</span>
                                            <span>{{ $log->created_at->format('d/m H:i') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-secondary-light text-center py-12 mb-0">Belum ada riwayat dosing.</p>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="text-center py-24">
                            <iconify-icon icon="mdi:pump-off" class="text-4xl text-secondary-light mb-8"></iconify-icon>
                            <p class="text-sm text-secondary-light mb-4">Pompa peristaltik belum terdaftar.</p>
                            <p class="text-xs text-secondary-light mb-0">Akan muncul setelah ESP32 pertama kali online.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Monitoring Listrik (PZEM-004T) ─── --}}
    <div class="mb-24">
        <div class="d-flex align-items-center gap-2 mb-16">
            <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                <iconify-icon icon="mdi:lightning-bolt-circle" class="text-warning-main"></iconify-icon>
                Monitoring Listrik
            </h6>
            <span class="text-xs px-8 py-4 rounded-pill bg-warning-focus text-warning-main fw-medium border br-warning">PZEM-004T</span>
        </div>
        <div class="row gy-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div class="w-48-px h-48-px bg-warning-focus text-warning-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:lightning-bolt" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Tegangan</p>
                        <h5 class="fw-bold mt-4 mb-0" id="voltage-detail">{{ $latestSensor?->voltage ?? '--' }} <span class="text-sm fw-normal text-secondary-light">V</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div class="w-48-px h-48-px bg-danger-focus text-danger-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:power-plug" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Daya</p>
                        <h5 class="fw-bold mt-4 mb-0" id="power-detail">{{ $latestSensor?->power ?? '--' }} <span class="text-sm fw-normal text-secondary-light">W</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div class="w-48-px h-48-px bg-info-focus text-info-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:current-ac" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Arus</p>
                        <h5 class="fw-bold mt-4 mb-0" id="current-detail">{{ $latestSensor?->current ?? '--' }} <span class="text-sm fw-normal text-secondary-light">A</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div class="w-48-px h-48-px bg-success-focus text-success-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:meter-electric" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Energi</p>
                        <h5 class="fw-bold mt-4 mb-0" id="energy-detail">{{ $latestSensor?->energy ?? '--' }} <span class="text-sm fw-normal text-secondary-light">kWh</span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Status Perangkat ─── --}}
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-20">
                <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="mdi:broadcast" class="text-info-main"></iconify-icon>
                    Status Perangkat
                </h6>
                {{-- @if($unresolvedAnomalies > 0)
                    <span class="badge bg-danger-focus text-danger-main rounded-pill px-12 py-4">
                        {{ $unresolvedAnomalies }} Anomali Aktif
                    </span>
                @else
                    <span class="badge bg-success-focus text-success-main rounded-pill px-12 py-4">
                        Kondisi Aman
                    </span>
                @endif --}}
            </div>
            <div class="row gy-4">
                @forelse($devices as $device)
                    <div class="col-xl-4 col-sm-6">
                        <div class="d-flex align-items-center gap-12 p-16 border radius-12">
                            <div class="w-44-px h-44-px {{ $device->type === 'sensor' ? 'bg-info-focus text-info-main' : 'bg-warning-focus text-warning-main' }} rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                <iconify-icon icon="{{ $device->type === 'sensor' ? 'mdi:chip' : 'mdi:water-pump' }}" class="text-xl"></iconify-icon>
                            </div>
                            <div class="flex-grow-1">
                                <p class="fw-semibold text-primary-light mb-0 text-sm">{{ $device->name }}</p>
                                <p class="text-xs text-secondary-light mb-0">{{ $device->last_heartbeat?->diffForHumans() ?? 'Belum pernah' }}</p>
                            </div>
                            <span class="badge {{ $device->is_online ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} rounded-pill px-10 py-4 text-xs">
                                {{ $device->is_online ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-sm text-secondary-light text-center py-24 mb-0">Belum ada perangkat terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const chartData = @json($chartData);
    const labels    = Object.keys(chartData);
    const phData    = labels.map(l => chartData[l].ph);
    const suhuData  = labels.map(l => chartData[l].suhu);
    const ppmData   = labels.map(l => chartData[l].ppm);
    const airData   = labels.map(l => chartData[l].water_level);

    const chartDefaults = {
        series: [],
        chart:  { type: 'area', height: 220, toolbar: { show: false } },
        dataLabels: { enabled: false },
        stroke:     { curve: 'smooth', width: 2 },
        fill:       { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
        xaxis: {
            categories: labels,
            labels: { style: { colors: '#6B7280', fontSize: '10px' } },
            axisBorder: { show: false },
            axisTicks:  { show: false },
        },
        yaxis:   { labels: { style: { colors: '#6B7280', fontSize: '10px' } } },
        grid:    { borderColor: '#f1f1f1' },
        tooltip: { x: { show: true } },
    };

    new ApexCharts(document.querySelector('#phChart'),   { ...chartDefaults, series: [{ name: 'pH', data: phData }],                colors: ['#06B6D4'] }).render();
    new ApexCharts(document.querySelector('#suhuChart'),  { ...chartDefaults, series: [{ name: 'Suhu (°C)', data: suhuData }],        colors: ['#8B5CF6'] }).render();
    new ApexCharts(document.querySelector('#ppmChart'),   { ...chartDefaults, series: [{ name: 'PPM/TDS', data: ppmData }],           colors: ['#10B981'] }).render();
    new ApexCharts(document.querySelector('#airChart'),   { ...chartDefaults, series: [{ name: 'Jarak (cm)', data: airData }],        colors: ['#3B82F6'] }).render();

    // Auto-refresh setiap 60 detik
    setInterval(() => {
        fetch('{{ route("sensor.latest") }}')
            .then(r => r.json())
            .then(data => {
                if (data.sensor) {
                    const s = data.sensor;
                    document.getElementById('ph-value').textContent    = s.ph   ?? '--';
                    document.getElementById('suhu-value').textContent  = s.suhu  ? s.suhu + '°C'  : '--';
                    document.getElementById('ppm-value').textContent   = s.ppm   ? s.ppm  + ' ppm': '--';
                    document.getElementById('air-value').textContent   = s.water_level !== null ? s.water_level + ' cm' : '--';
                    document.getElementById('voltage-value').textContent = s.voltage ? s.voltage + ' V' : '--';
                    document.getElementById('voltage-detail').innerHTML  = (s.voltage ?? '--') + ' <span class="text-sm fw-normal text-secondary-light">V</span>';
                    document.getElementById('power-detail').innerHTML    = (s.power   ?? '--') + ' <span class="text-sm fw-normal text-secondary-light">W</span>';
                    document.getElementById('current-detail').innerHTML  = (s.current ?? '--') + ' <span class="text-sm fw-normal text-secondary-light">A</span>';
                    document.getElementById('energy-detail').innerHTML   = (s.energy  ?? '--') + ' <span class="text-sm fw-normal text-secondary-light">kWh</span>';

                    if (data.configs) {
                        const updateBadge = (id, value, cfg) => {
                            const badge = document.getElementById(id + '-badge');
                            if (badge && cfg && value !== null) {
                                const ok = value >= cfg.min_optimal && value <= cfg.max_optimal;
                                badge.className = `d-inline-flex align-items-center gap-1 ${ok ? 'text-success-main' : 'text-danger-main'}`;
                                badge.innerHTML = `<iconify-icon icon="${ok ? 'bxs:up-arrow':'bxs:down-arrow'}" class="text-xs"></iconify-icon> ${ok ? 'Normal' : 'Perhatian'}`;
                            }
                        };
                        updateBadge('ph',   s.ph,   data.configs.ph);
                        updateBadge('suhu', s.suhu, data.configs.suhu);
                        updateBadge('ppm',  s.ppm,  data.configs.ppm);
                    }
                }
                document.getElementById('last-update').textContent = 'Terakhir update: ' + new Date().toLocaleTimeString('id-ID');
            })
            .catch(() => {
                document.getElementById('last-update').textContent = 'Gagal update — periksa koneksi';
            });
    }, 60000);
</script>
@endpush