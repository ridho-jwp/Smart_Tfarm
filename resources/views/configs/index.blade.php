@extends('layouts.app')
@section('title', 'Konfigurasi — Smart Pakcoy')

@section('content')

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Konfigurasi Threshold</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Konfigurasi</li>
        </ul>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-8">
            <div class="card">
                <div class="card-body p-32">
                    <div class="d-flex align-items-center gap-3 mb-32">
                        <div
                            class="w-48-px h-48-px bg-primary-100 text-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="icon-park-outline:setting-two" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-4">Pengaturan Batas Optimal Sensor</h5>
                            <p class="text-secondary-light text-sm mb-0">Atur rentang nilai ideal untuk setiap parameter
                                sensor tanaman.</p>
                        </div>
                    </div>

                    <form action="{{ route('configs.update') }}" method="POST">
                        @csrf

                        @foreach([
                                ['key' => 'ph',             'label' => 'pH Air',                 'icon' => 'fluent:drop-20-filled',        'color' => 'info',    'desc' => 'Tingkat keasaman larutan nutrisi'],
                                ['key' => 'suhu',           'label' => 'Suhu Air',               'icon' => 'mdi:thermometer',              'color' => 'warning', 'desc' => 'Suhu larutan nutrisi dalam °C'],
                                ['key' => 'ppm',            'label' => 'PPM Nutrisi',            'icon' => 'mdi:flask',                    'color' => 'success', 'desc' => 'Konsentrasi larutan nutrisi dalam ppm'],
                                ['key' => 'ketinggian_air', 'label' => 'Ketinggian Air Tandon',  'icon' => 'mdi:water-pump',               'color' => 'primary', 'desc' => 'Batas ketinggian air tandon (pompa otomatis)'],
                            ] as $param)

                            @php $cfg = $configs[$param['key']] ?? null; @endphp
                            <div class="border radius-12 p-24 mb-24">
                                <div class="d-flex align-items-center gap-3 mb-20">
                                    <div class="w-40-px h-40-px bg-{{ $param['color'] }}-focus text-{{ $param['color'] }}-main rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="{{ $param['icon'] }}" class="text-lg"></iconify-icon>

                                                                       </div>
                                    <div>
                                        <h6 class="fw-semibold mb-0">{{ $param['label'] }}</h6>
                                        <p class="text-xs text-secondary-light mb-0">{{ $param['desc'] }} @if($cfg) · Satuan: <strong>{{ $cfg->unit ?? '-' }}</strong> @endif</p>
                                    </div>

                                                                   </div>
                                <div class="row gy-3">



                                                      <div class="col-sm-6">
                                        <label class="form-label text-sm fw-medium text-secondary-light text-uppercase">Batas Minimum (Ideal)</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="{{ $param['key'] }}_min" value="{{ $cfg?->min_optimal }}" class="form-control radius-8 @error($param['key'] . '_min') is-invalid @enderror" placeholder="Minimum" required>
                                            @if($cfg?->unit)
                                                <span class="input-group-text text-secondary-light">{{ $cfg->unit }}</span>
                                            @endif
                                        </div>
                                        @error($param['key'] . '_min')
                                            <span class="text-danger-main text-xs d-block mt-4">{{ $message }}</span>

                                        @enderror
                                    </div>



                                                                           <div class="col-sm-6">
                                        <label class="form-label text-sm fw-medium text-secondary-light text-uppercase">Batas Maksimum (Ideal)</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="{{ $param['key'] }}_max" value="{{ $cfg?->max_optimal }}" class="form-control radius-8 @error($param['key'] . '_max') is-invalid @enderror" placeholder="Maksimum" required>
                                            @if($cfg?->unit)
                                                <span class="input-group-text text-secondary-light">{{ $cfg->unit }}</span>
                                            @endif
                                        </div>
                                        @error($param['key'] . '_max')
                                            <span class="text-danger-main text-xs d-block mt-4">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="d-flex align-items-center justify-content-between pt-8">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary radius-8 px-20">
                                <iconify-icon icon="solar:arrow-left-outline" class="me-2"></iconify-icon>
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary radius-8 px-24">
                                <iconify-icon icon="mdi:content-save" class="me-2 text-lg"></iconify-icon>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection