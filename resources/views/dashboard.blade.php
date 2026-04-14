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

    {{-- Sensor Cards --}}
    @php
        $phMin = $configs['ph']->min_optimal ?? 5.5;
        $phMax = $configs['ph']->max_optimal ?? 6.5;
        $suhuMin = $configs['suhu']->min_optimal ?? 20;
        $suhuMax = $configs['suhu']->max_optimal ?? 30;
        $ppmMin = $configs['ppm']->min_optimal ?? 500;
        $ppmMax = $configs['ppm']->max_optimal ?? 1200;

        $ph = $latestSensor?->ph;
        $suhu = $latestSensor?->suhu;
        $ppm = $latestSensor?->ppm;

        $phStatus = $ph !== null && $ph >= $phMin && $ph <= $phMax;
        $suhuStatus = $suhu !== null && $suhu >= $suhuMin && $suhu <= $suhuMax;
        $ppmStatus = $ppm !== null && $ppm >= $ppmMin && $ppm <= $ppmMax;
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
                            <p class="text-xs text-secondary-light mt-1 mb-0" id="ph-optimal">Optimal: {{ $phMin }} –
                                {{ $phMax }}
                            </p>
                        </div>
                        <div
                            class="w-44-px h-44-px bg-cyan-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="fluent:drop-20-filled" class="text-cyan text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    @if($latestSensor)
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span id="ph-badge"
                                class="d-inline-flex align-items-center gap-1 {{ $phStatus ? 'text-success-main' : 'text-danger-main' }}">
                                <iconify-icon icon="{{ $phStatus ? 'bxs:up-arrow' : 'bxs:down-arrow' }}"
                                    class="text-xs"></iconify-icon>
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
                            <p class="text-xs text-secondary-light mt-1 mb-0" id="suhu-optimal">Optimal: {{ $suhuMin }} –
                                {{ $suhuMax }}°C
                            </p>
                        </div>
                        <div
                            class="w-44-px h-44-px bg-purple-light rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:thermometer" class="text-purple text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    @if($latestSensor)
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span id="suhu-badge"
                                class="d-inline-flex align-items-center gap-1 {{ $suhuStatus ? 'text-success-main' : 'text-danger-main' }}">
                                <iconify-icon icon="{{ $suhuStatus ? 'bxs:up-arrow' : 'bxs:down-arrow' }}"
                                    class="text-xs"></iconify-icon>
                                {{ $suhuStatus ? 'Normal' : 'Perhatian' }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- PPM Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-3 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">PPM Nutrisi</p>
                            <h4 class="mb-0 fw-bold" id="ppm-value">{{ $ppm ? $ppm . ' ppm' : '--' }}</h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0" id="ppm-optimal">Optimal: {{ $ppmMin }} –
                                {{ $ppmMax }} ppm
                            </p>
                        </div>
                        <div
                            class="w-44-px h-44-px bg-info-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:flask" class="text-info-main text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    @if($latestSensor)
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span id="ppm-badge"
                                class="d-inline-flex align-items-center gap-1 {{ $ppmStatus ? 'text-success-main' : 'text-danger-main' }}">
                                <iconify-icon icon="{{ $ppmStatus ? 'bxs:up-arrow' : 'bxs:down-arrow' }}"
                                    class="text-xs"></iconify-icon>
                                {{ $ppmStatus ? 'Normal' : 'Perhatian' }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Voltage Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Tegangan</p>
                            <h4 class="mb-0 fw-bold" id="voltage-value">
                                {{ $latestSensor?->voltage ? $latestSensor->voltage . ' V' : '--' }}
                            </h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0">Optimal: 190 – 240V</p>
                        </div>
                        <div
                            class="w-44-px h-44-px bg-success-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:lightning-bolt" class="text-success-main text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Power Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-5 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Daya</p>
                            <h4 class="mb-0 fw-bold" id="power-value">
                                {{ $latestSensor?->power ? $latestSensor->power . ' W' : '--' }}
                            </h4>
                            <p class="text-xs text-secondary-light mt-1 mb-0">Real-time</p>
                        </div>
                        <div
                            class="w-44-px h-44-px bg-danger-focus rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="mdi:power-plug" class="text-danger-main text-xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row gy-4 mb-24">
        <div class="col-xxl-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-16">
                        <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:graph-new-up-outline" class="text-info-main"></iconify-icon>
                            Grafik pH (24 Jam)
                        </h6>
                        <span
                            class="text-sm fw-semibold rounded-pill bg-info-focus text-info-main border br-info px-8 py-4">pH
                            Air</span>
                    </div>
                    <div id="phChart" style="height:220px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-16">
                        <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:graph-new-up-outline" class="text-success-main"></iconify-icon>
                            Grafik PPM (24 Jam)
                        </h6>
                        <span
                            class="text-sm fw-semibold rounded-pill bg-success-focus text-success-main border br-success px-8 py-4">Nutrisi</span>
                    </div>
                    <div id="ppmChart" style="height:220px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Electricity Monitoring --}}
    <div class="mb-24">
        <div class="d-flex align-items-center gap-2 mb-16">
            <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                <iconify-icon icon="mdi:lightning-bolt-circle" class="text-warning-main"></iconify-icon>
                Monitoring Listrik
            </h6>
            <span
                class="text-xs px-8 py-4 rounded-pill bg-warning-focus text-warning-main fw-medium border br-warning">PZEM-004T</span>
            <span class="text-xs text-secondary-light" id="last-update">Update otomatis setiap 1 menit</span>
        </div>
        <div class="row gy-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div
                            class="w-48-px h-48-px bg-warning-focus text-warning-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:lightning-bolt" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Tegangan</p>
                        <h5 class="fw-bold mt-4 mb-0" id="voltage-detail">{{ $latestSensor?->voltage ?? '--' }} <span
                                class="text-sm fw-normal text-secondary-light">V</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div
                            class="w-48-px h-48-px bg-danger-focus text-danger-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:power-plug" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Daya</p>
                        <h5 class="fw-bold mt-4 mb-0" id="power-detail">{{ $latestSensor?->power ?? '--' }} <span
                                class="text-sm fw-normal text-secondary-light">W</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div
                            class="w-48-px h-48-px bg-info-focus text-info-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:current-ac" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Arus</p>
                        <h5 class="fw-bold mt-4 mb-0" id="current-value">{{ $latestSensor?->current ?? '--' }} <span
                                class="text-sm fw-normal text-secondary-light">A</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-none border text-center h-100">
                    <div class="card-body p-20">
                        <div
                            class="w-48-px h-48-px bg-success-focus text-success-main rounded-circle d-flex justify-content-center align-items-center mx-auto mb-16">
                            <iconify-icon icon="mdi:meter-electric" class="text-2xl"></iconify-icon>
                        </div>
                        <p class="text-secondary-light text-sm mb-0">Energi</p>
                        <h5 class="fw-bold mt-4 mb-0" id="energy-value">{{ $latestSensor?->energy ?? '--' }} <span
                                class="text-sm fw-normal text-secondary-light">kWh</span></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4 mt-2">
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-16">
                            <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:graph-new-up-outline" class="text-warning-main"></iconify-icon>
                                Grafik Tegangan (24 Jam)
                            </h6>
                            <span class="text-xs text-warning-main fw-semibold">Volt</span>
                        </div>
                        <div id="voltageChart" style="height:220px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-16">
                            <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:graph-new-up-outline" class="text-danger-main"></iconify-icon>
                                Grafik Daya (24 Jam)
                            </h6>
                            <span class="text-xs text-danger-main fw-semibold">Watt</span>
                        </div>
                        <div id="powerChart" style="height:220px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pump Control --}}
    <div class="row gy-4 mb-24">
        {{-- Pompa Nutrisi --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-20">
                        <div
                            class="w-48-px h-48-px bg-success-focus text-success-main rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="mdi:water-pump" class="text-xl"></iconify-icon>
                        </div>
                        <div class="flex-1">
                            <h6 class="fw-semibold mb-0">Pompa Nutrisi</h6>
                            <p class="text-xs text-secondary-light mb-0">Kontrol pompa air nutrisi</p>
                        </div>
                        @if($nutrisiPump)
                            <span
                                class="badge {{ $nutrisiPump->is_online ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} rounded-pill px-12 py-4 text-sm">
                                {{ $nutrisiPump->is_online ? 'Online' : 'Offline' }}
                            </span>
                        @endif
                    </div>

                    @if($nutrisiPump)
                        <div class="d-flex gap-12 mb-20">
                            <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="device_id" value="{{ $nutrisiPump->id }}">
                                <input type="hidden" name="action" value="pump_on">
                                <button type="submit"
                                    class="btn btn-outline-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="mdi:lightning-bolt" class="text-lg"></iconify-icon> ON
                                </button>
                            </form>
                            <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="device_id" value="{{ $nutrisiPump->id }}">
                                <input type="hidden" name="action" value="pump_off">
                                <button type="submit"
                                    class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="mdi:power-off" class="text-lg"></iconify-icon> OFF
                                </button>
                            </form>
                        </div>
                        <div class="border-top pt-16">
                            <h6 class="text-xs fw-semibold text-secondary-light text-uppercase mb-12">Riwayat Nyala/Mati</h6>
                            <div class="max-h-200-px overflow-y-auto scroll-sm">
                                @forelse($nutrisiLogs as $log)
                                    <div
                                        class="d-flex align-items-center justify-content-between py-8 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex align-items-center gap-8">
                                            <span
                                                class="w-8-px h-8-px rounded-circle {{ $log->action === 'pump_on' ? 'bg-success-main' : 'bg-danger-main' }}"></span>
                                            <span
                                                class="text-sm text-secondary-light">{{ $log->action === 'pump_on' ? 'Nyala' : 'Mati' }}</span>
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
                        <p class="text-sm text-secondary-light text-center py-24 mb-0">Pompa nutrisi belum terdaftar.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pompa Pembasmi Hama --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-20">
                        <div
                            class="w-48-px h-48-px bg-danger-focus text-danger-main rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="mdi:spray" class="text-xl"></iconify-icon>
                        </div>
                        <div class="flex-1">
                            <h6 class="fw-semibold mb-0">Pompa Pembasmi Hama</h6>
                            <p class="text-xs text-secondary-light mb-0">Kontrol penyiraman pembasmi hama</p>
                        </div>
                        @if($hamaPump)
                            <span
                                class="badge {{ $hamaPump->is_online ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} rounded-pill px-12 py-4 text-sm">
                                {{ $hamaPump->is_online ? 'Online' : 'Offline' }}
                            </span>
                        @endif
                    </div>

                    @if($hamaPump)
                        <div class="d-flex gap-12 mb-20">
                            <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="device_id" value="{{ $hamaPump->id }}">
                                <input type="hidden" name="action" value="spray_on">
                                <button type="submit"
                                    class="btn btn-outline-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="mdi:lightning-bolt" class="text-lg"></iconify-icon> ON
                                </button>
                            </form>
                            <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="device_id" value="{{ $hamaPump->id }}">
                                <input type="hidden" name="action" value="spray_off">
                                <button type="submit"
                                    class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="mdi:power-off" class="text-lg"></iconify-icon> OFF
                                </button>
                            </form>
                        </div>
                        <div class="border-top pt-16">
                            <h6 class="text-xs fw-semibold text-secondary-light text-uppercase mb-12">Riwayat Nyala/Mati</h6>
                            <div class="max-h-200-px overflow-y-auto scroll-sm">
                                @forelse($hamaLogs as $log)
                                    <div
                                        class="d-flex align-items-center justify-content-between py-8 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex align-items-center gap-8">
                                            <span
                                                class="w-8-px h-8-px rounded-circle {{ in_array($log->action, ['spray_on', 'pump_on']) ? 'bg-success-main' : 'bg-danger-main' }}"></span>
                                            <span
                                                class="text-sm text-secondary-light">{{ in_array($log->action, ['spray_on', 'pump_on']) ? 'Nyala' : 'Mati' }}</span>
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
                        <p class="text-sm text-secondary-light text-center py-24 mb-0">Pompa pembasmi hama belum terdaftar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Device Status --}}
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-20">
                <h6 class="fw-semibold mb-0">📡 Status Perangkat</h6>
                {{-- @if($unresolvedAnomalies > 0)
                    <span class="badge bg-danger-focus text-danger-main rounded-pill px-12 py-4">
                        {{ $unresolvedAnomalies }} Hama Aktif
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
                            <div
                                class="w-44-px h-44-px {{ $device->type === 'sensor' ? 'bg-info-focus text-info-main' : ($device->type === 'camera' ? 'bg-purple-light text-purple' : 'bg-warning-focus text-warning-main') }} rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                @if($device->type === 'sensor')
                                    <iconify-icon icon="mdi:chip" class="text-xl"></iconify-icon>
                                @elseif($device->type === 'camera')
                                    <iconify-icon icon="mdi:camera" class="text-xl"></iconify-icon>
                                @else
                                    <iconify-icon icon="mdi:water-pump" class="text-xl"></iconify-icon>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <p class="fw-semibold text-primary-light mb-0 text-sm">{{ $device->name }}</p>
                                <p class="text-xs text-secondary-light mb-0">
                                    {{ $device->last_heartbeat?->diffForHumans() ?? 'Belum pernah' }}
                                </p>
                            </div>
                            <span
                                class="badge {{ $device->is_online ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} rounded-pill px-10 py-4 text-xs">
                                {{ $device->is_online ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-sm text-secondary-light text-center py-24 mb-0">Belum ada perangkat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const chartData = @json($chartData);
        const labels = Object.keys(chartData);
        const phData = labels.map(l => chartData[l].ph);
        const ppmData = labels.map(l => chartData[l].ppm);
        const voltageData = labels.map(l => chartData[l].voltage ?? null);
        const powerData = labels.map(l => chartData[l].power ?? null);

        const chartDefaults = {
            series: [],
            chart: { type: 'area', height: 220, toolbar: { show: false }, sparkline: { enabled: false } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.1 } },
            xaxis: { categories: labels, labels: { style: { colors: '#6B7280', fontSize: '10px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: '#6B7280', fontSize: '10px' } } },
            grid: { borderColor: '#f1f1f1' },
            tooltip: { x: { show: true } }
        };

        // pH Chart
        new ApexCharts(document.querySelector('#phChart'), {
            ...chartDefaults,
            series: [{ name: 'pH', data: phData }],
            colors: ['#3B82F6'],
        }).render();

        // PPM Chart
        new ApexCharts(document.querySelector('#ppmChart'), {
            ...chartDefaults,
            series: [{ name: 'PPM', data: ppmData }],
            colors: ['#8B5CF6'],
        }).render();

        // Voltage Chart
        new ApexCharts(document.querySelector('#voltageChart'), {
            ...chartDefaults,
            series: [{ name: 'Tegangan (V)', data: voltageData }],
            colors: ['#F59E0B'],
        }).render();

        // Power Chart
        new ApexCharts(document.querySelector('#powerChart'), {
            ...chartDefaults,
            series: [{ name: 'Daya (W)', data: powerData }],
            colors: ['#EF4444'],
        }).render();

        // Auto-refresh setiap 60 detik
        setInterval(() => {
            fetch('{{ route("sensor.latest") }}')
                .then(r => r.json())
                .then(data => {
                    if (data.sensor) {
                        document.getElementById('ph-value').textContent = data.sensor.ph ?? '--';
                        document.getElementById('suhu-value').textContent = data.sensor.suhu ? data.sensor.suhu + '°C' : '--';
                        document.getElementById('ppm-value').textContent = data.sensor.ppm ? data.sensor.ppm + ' ppm' : '--';
                        if (data.sensor.voltage !== null) document.getElementById('voltage-value').textContent = data.sensor.voltage + ' V';
                        if (data.sensor.power !== null) document.getElementById('power-value').textContent = data.sensor.power + ' W';
                        if (data.sensor.current !== null) document.getElementById('current-value').textContent = data.sensor.current + ' A';
                        if (data.sensor.energy !== null) document.getElementById('energy-value').textContent = data.sensor.energy + ' kWh';

                        if (data.configs) {
                            const updateBadge = (id, value, cfg) => {
                                const badge = document.getElementById(id + '-badge');
                                if (badge && cfg) {
                                    const isNormal = value >= cfg.min_optimal && value <= cfg.max_optimal;
                                    badge.className = `d-inline-flex align-items-center gap-1 ${isNormal ? 'text-success-main' : 'text-danger-main'}`;
                                    badge.innerHTML = `<iconify-icon icon="${isNormal ? 'bxs:up-arrow' : 'bxs:down-arrow'}" class="text-xs"></iconify-icon> ${isNormal ? 'Normal' : 'Perhatian'}`;
                                    const optEl = document.getElementById(id + '-optimal');
                                    if (optEl) optEl.textContent = `Optimal: ${cfg.min_optimal} – ${cfg.max_optimal}${cfg.unit ? ' ' + cfg.unit : ''}`;
                                }
                            };
                            updateBadge('ph', data.sensor.ph, data.configs.ph);
                            updateBadge('suhu', data.sensor.suhu, data.configs.suhu);
                            updateBadge('ppm', data.sensor.ppm, data.configs.ppm);
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