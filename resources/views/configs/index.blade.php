@extends('layouts.app')
@section('title', 'Konfigurasi — Smart Pakcoy')

@section('content')

    {{-- ── Breadcrumb ─────────────────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Konfigurasi Sistem</h6>
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

    {{-- Info: preset tidak mempengaruhi ketinggian air --}}
    <div class="alert alert-info d-flex align-items-center gap-2 mb-20 radius-8" style="font-size:.875rem;">
        <iconify-icon icon="solar:info-circle-outline" class="text-lg flex-shrink-0"></iconify-icon>
        <span>
            <strong>Catatan:</strong> Preset sensor hanya mencakup <strong>pH, Suhu Air, dan PPM Nutrisi</strong>.
            Pengaturan <strong>Ketinggian Air Tandon</strong> dikelola terpisah di tab <em>Input Manual</em>.
        </span>
    </div>

    {{-- ── Tab Navigation ──────────────────────────────────────────────────── --}}
    <ul class="nav nav-pills mb-24 gap-2" id="configTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center gap-2" id="tab-preset-btn" data-bs-toggle="pill"
                data-bs-target="#tab-preset" type="button">
                <iconify-icon icon="solar:bolt-outline" class="text-lg"></iconify-icon>
                Pilih Preset
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-2" id="tab-kelola-btn" data-bs-toggle="pill"
                data-bs-target="#tab-kelola" type="button">
                <iconify-icon icon="solar:list-check-outline" class="text-lg"></iconify-icon>
                Kelola Preset
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-2" id="tab-manual-btn" data-bs-toggle="pill"
                data-bs-target="#tab-manual" type="button">
                <iconify-icon icon="solar:waterdrop-outline" class="text-lg"></iconify-icon>
                Ketinggian Air
            </button>
        </li>
    </ul>

    {{-- ── Tab Content ─────────────────────────────────────────────────────── --}}
    <div class="tab-content" id="configTabContent">

        {{-- ════════════════════════════════════════════════════════════════════
             TAB 1 — PILIH PRESET
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade show active" id="tab-preset" role="tabpanel">
            <div class="card">
                <div class="card-body p-32">
                    <div class="d-flex align-items-center gap-3 mb-24">
                        <div
                            class="w-48-px h-48-px bg-primary-100 text-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:bolt-outline" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-4">Terapkan Preset Cepat</h5>
                            <p class="text-secondary-light text-sm mb-0">Pilih preset sesuai usia tanaman, klik
                                <strong>Terapkan</strong> untuk langsung mengupdate threshold pH, Suhu, dan PPM.
                            </p>
                        </div>
                    </div>

                    {{-- Konfigurasi Aktif Sekarang (hanya 3 sensor preset) --}}
                    <div class="border radius-12 p-20 mb-28" style="background:var(--primary-50,#f0fdf4);">
                        <h6 class="fw-semibold mb-16 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:settings-outline" class="text-primary-600"></iconify-icon>
                            Konfigurasi Sensor Aktif Saat Ini
                            @if($activePreset)
                                <span class="badge bg-success-focus text-success-main text-xs ms-2 border border-success">Preset Aktif: {{ $activePreset->name }}</span>
                            @else
                                <span class="badge bg-warning-focus text-warning-main text-xs ms-2">Kustom / Manual</span>
                            @endif
                        </h6>
                        <div class="row gy-3 text-sm">
                            @php
                                $activeParams = [
                                    [
                                        'key' => 'ph',
                                        'label' => 'pH Air',
                                        'icon' => 'fluent:drop-20-filled',
                                        'color' => 'info',
                                    ],
                                    [
                                        'key' => 'suhu',
                                        'label' => 'Suhu Air',
                                        'icon' => 'mdi:thermometer',
                                        'color' => 'warning',
                                    ],
                                    [
                                        'key' => 'ppm',
                                        'label' => 'PPM Nutrisi',
                                        'icon' => 'mdi:flask',
                                        'color' => 'success',
                                    ],
                                ];
                            @endphp
                            @foreach ($activeParams as $p)
                                @php $c = $configs[$p['key']] ?? null; @endphp
                                <div class="col-sm-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            class="w-36-px h-36-px bg-{{ $p['color'] }}-focus text-{{ $p['color'] }}-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            <iconify-icon icon="{{ $p['icon'] }}" class="text-sm"></iconify-icon>
                                        </div>
                                        <div>
                                            <p class="text-xs text-secondary-light mb-0">{{ $p['label'] }}</p>
                                            <p class="fw-semibold mb-0 text-sm">
                                                {{ $c ? number_format($c->min_optimal, 1) . ' – ' . number_format($c->max_optimal, 1) . ' ' . ($c->unit ?? '') : '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Preset Cards --}}
                    @if ($presets->isEmpty())
                        <div class="text-center py-40 text-secondary-light">
                            <iconify-icon icon="solar:documents-outline" class="text-4xl mb-12"></iconify-icon>
                            <p>Belum ada preset. Tambahkan di tab <strong>Kelola Preset</strong>.</p>
                        </div>
                    @else
                        <div class="row gy-16">
                            @foreach ($presets as $preset)
                                <div class="col-sm-6 col-xl-3">
                                    <div class="border radius-12 p-20 h-100 d-flex flex-column preset-card @if($activePreset && $activePreset->id == $preset->id) border-success @endif"
                                        style="transition:box-shadow .2s,border-color .2s; @if($activePreset && $activePreset->id == $preset->id) background: var(--success-50, #f0fdf4); @endif">

                                        <div class="d-flex gap-2 mb-12 align-self-start">
                                            @if ($preset->is_default)
                                                <span class="badge bg-primary-100 text-primary-600 text-xs">Bawaan</span>
                                            @else
                                                <span class="badge bg-success-focus text-success-main text-xs">Kustom</span>
                                            @endif
                                            
                                            @if($activePreset && $activePreset->id == $preset->id)
                                                <span class="badge bg-success text-white text-xs">Sedang Digunakan</span>
                                            @endif
                                        </div>

                                        <h6 class="fw-bold mb-4">{{ $preset->name }}</h6>
                                        <p class="text-secondary-light text-xs mb-16 flex-grow-1">
                                            {{ $preset->description ?: '—' }}
                                        </p>

                                        {{-- Mini stats — TANPA ketinggian air --}}
                                        <div class="row gy-2 mb-16 text-xs">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between py-6 border-bottom">
                                                    <span class="text-secondary-light d-flex align-items-center gap-1">
                                                        <iconify-icon icon="fluent:drop-20-filled"
                                                            class="text-info-main"></iconify-icon> pH
                                                    </span>
                                                    <strong>{{ number_format($preset->ph_min, 1) }} –
                                                        {{ number_format($preset->ph_max, 1) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between py-6 border-bottom">
                                                    <span class="text-secondary-light d-flex align-items-center gap-1">
                                                        <iconify-icon icon="mdi:thermometer"
                                                            class="text-warning-main"></iconify-icon> Suhu
                                                    </span>
                                                    <strong>{{ number_format($preset->suhu_min, 1) }} –
                                                        {{ number_format($preset->suhu_max, 1) }} °C</strong>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between py-6">
                                                    <span class="text-secondary-light d-flex align-items-center gap-1">
                                                        <iconify-icon icon="mdi:flask"
                                                            class="text-success-main"></iconify-icon> PPM
                                                    </span>
                                                    <strong>{{ (int) $preset->ppm_min }} – {{ (int) $preset->ppm_max }}
                                                        ppm</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 w-100 mt-16">
                                            {{-- Ubah button menjadi elemen 'a' agar bisa menggunakan href --}}
                                            <a href="{{ route('configs.pestisida', ['id' => $preset->id]) }}"
                                                class="d-flex flex-column align-items-center justify-content-center w-50 py-12 border radius-12 text-secondary-light bg-hover-neutral-50 text-decoration-none"
                                                style="border: 1px solid #e5e7eb !important;">

                                                <iconify-icon icon="solar:pen-new-square-bold"
                                                    class="text-xl mb-4"></iconify-icon>
                                                <span class="text-xs fw-medium">Input Pestisida</span>

                                            </a>

                                            {{-- Tombol 2: Aktifkan --}}
                                            <form action="{{ route('configs.preset.apply') }}" method="POST" class="w-50 m-0">
                                                @csrf
                                                <input type="hidden" name="preset_id" value="{{ $preset->id }}">
                                                <button type="submit"
                                                    class="d-flex flex-column align-items-center justify-content-center w-100 py-12 rounded-xl text-white border-0 transition-opacity hover-opacity-90"
                                                    style="background-color: #16a34a !important;">
                                                    <iconify-icon icon="solar:bolt-bold"
                                                        class="text-xl mb-4"></iconify-icon>
                                                    <span class="text-xs fw-medium">Aktifkan</span>
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════
             TAB 2 — KELOLA PRESET
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="tab-kelola" role="tabpanel">
            <div class="row gy-24">

                {{-- Daftar Preset --}}
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body p-32">
                            <div class="d-flex align-items-center gap-3 mb-24">
                                <div
                                    class="w-48-px h-48-px bg-success-focus text-success-main rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="solar:list-check-outline" class="text-xl"></iconify-icon>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-4">Daftar Preset</h5>
                                    <p class="text-secondary-light text-sm mb-0">{{ $presets->count() }} preset tersimpan.
                                        Semua preset dapat dihapus.</p>
                                </div>
                            </div>

                            @if ($presets->isEmpty())
                                <div class="text-center py-40 text-secondary-light">
                                    <iconify-icon icon="solar:documents-outline" class="text-4xl mb-12"></iconify-icon>
                                    <p>Belum ada preset. Tambahkan di form sebelah kanan.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Nama Preset</th>
                                                <th class="text-center">pH</th>
                                                <th class="text-center">Suhu °C</th>
                                                <th class="text-center">PPM</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($presets as $preset)
                                                <tr>
                                                    <td>
                                                        <span class="fw-medium d-block">{{ $preset->name }}</span>
                                                        @if ($preset->description)
                                                            <span
                                                                class="text-xs text-secondary-light">{{ Str::limit($preset->description, 55) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-sm">
                                                        {{ number_format($preset->ph_min, 1) }}–{{ number_format($preset->ph_max, 1) }}
                                                    </td>
                                                    <td class="text-center text-sm">
                                                        {{ number_format($preset->suhu_min, 1) }}–{{ number_format($preset->suhu_max, 1) }}
                                                    </td>
                                                    <td class="text-center text-sm">
                                                        {{ (int) $preset->ppm_min }}–{{ (int) $preset->ppm_max }}
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <form action="{{ route('configs.preset.apply') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="preset_id"
                                                                    value="{{ $preset->id }}">
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-sm radius-8"
                                                                    title="Terapkan preset ini">
                                                                    <iconify-icon icon="solar:bolt-outline"></iconify-icon>
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-warning-focus btn-sm radius-8 btn-edit-preset"
                                                                title="Edit preset"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editPresetModal"
                                                                data-preset="{{ json_encode($preset) }}">
                                                                <iconify-icon icon="solar:pen-bold" class="text-warning-main"></iconify-icon>
                                                            </button>
                                                            <form action="{{ route('configs.preset.destroy', $preset) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Hapus preset « {{ $preset->name }} »?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger-focus btn-sm radius-8"
                                                                    title="Hapus preset">
                                                                    <iconify-icon icon="solar:trash-bin-trash-outline"
                                                                        class="text-danger-main"></iconify-icon>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Form Tambah Preset Baru --}}
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body p-32">
                            <div class="d-flex align-items-center gap-3 mb-24">
                                <div
                                    class="w-48-px h-48-px bg-warning-focus text-warning-main rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-4">Tambah Preset Baru</h5>
                                    <p class="text-secondary-light text-sm mb-0">Preset hanya mencakup pH, Suhu Air, dan
                                        PPM Nutrisi.</p>
                                </div>
                            </div>

                            <form id="formAddPreset" action="{{ route('configs.preset.store') }}" method="POST"
                                class="needs-decimal-fix">
                                @csrf

                                <div class="mb-16">
                                    <label class="form-label text-sm fw-medium">Nama Preset <span
                                            class="text-danger-main">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="form-control radius-8 @error('name') is-invalid @enderror"
                                        placeholder="contoh: Pakcoy Usia 3 Minggu" required>
                                    @error('name')
                                        <span class="text-danger-main text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-20">
                                    <label class="form-label text-sm fw-medium">Deskripsi</label>
                                    <textarea name="description" rows="2" class="form-control radius-8"
                                        placeholder="Fase pertumbuhan / keterangan…">{{ old('description') }}</textarea>
                                </div>

                                {{-- pH --}}
                                <div class="border radius-8 p-16 mb-12">
                                    <p class="text-sm fw-semibold mb-10 d-flex align-items-center gap-2">
                                        <iconify-icon icon="fluent:drop-20-filled" class="text-info-main"></iconify-icon>
                                        pH Air <small class="text-secondary-light fw-normal">(rentang 0 – 14)</small>
                                    </p>
                                    <div class="row gy-2">
                                        <div class="col-6">
                                            <label class="form-label text-xs text-secondary-light mb-4">Min</label>
                                            <input type="text" inputmode="decimal" name="ph_min"
                                                value="{{ old('ph_min') }}"
                                                class="form-control radius-8 decimal-input @error('ph_min') is-invalid @enderror"
                                                placeholder="contoh: 5.5" data-min="0" data-max="14" required>
                                            @error('ph_min')
                                                <span class="text-danger-main text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-xs text-secondary-light mb-4">Max</label>
                                            <input type="text" inputmode="decimal" name="ph_max"
                                                value="{{ old('ph_max') }}"
                                                class="form-control radius-8 decimal-input @error('ph_max') is-invalid @enderror"
                                                placeholder="contoh: 6.5" data-min="0" data-max="14" required>
                                            @error('ph_max')
                                                <span class="text-danger-main text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Suhu --}}
                                <div class="border radius-8 p-16 mb-12">
                                    <p class="text-sm fw-semibold mb-10 d-flex align-items-center gap-2">
                                        <iconify-icon icon="mdi:thermometer" class="text-warning-main"></iconify-icon>
                                        Suhu Air <small class="text-secondary-light fw-normal">(°C)</small>
                                    </p>
                                    <div class="row gy-2">
                                        <div class="col-6">
                                            <label class="form-label text-xs text-secondary-light mb-4">Min</label>
                                            <div class="input-group">
                                                <input type="text" inputmode="decimal" name="suhu_min"
                                                    value="{{ old('suhu_min') }}"
                                                    class="form-control radius-8 decimal-input @error('suhu_min') is-invalid @enderror"
                                                    placeholder="20" required>
                                                <span class="input-group-text text-xs">°C</span>
                                            </div>
                                            @error('suhu_min')
                                                <span class="text-danger-main text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-xs text-secondary-light mb-4">Max</label>
                                            <div class="input-group">
                                                <input type="text" inputmode="decimal" name="suhu_max"
                                                    value="{{ old('suhu_max') }}"
                                                    class="form-control radius-8 decimal-input @error('suhu_max') is-invalid @enderror"
                                                    placeholder="28" required>
                                                <span class="input-group-text text-xs">°C</span>
                                            </div>
                                            @error('suhu_max')
                                                <span class="text-danger-main text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- PPM --}}
                                <div class="border radius-8 p-16 mb-20">
                                    <p class="text-sm fw-semibold mb-10 d-flex align-items-center gap-2">
                                        <iconify-icon icon="mdi:flask" class="text-success-main"></iconify-icon>
                                        PPM Nutrisi <small class="text-secondary-light fw-normal">(bilangan bulat)</small>
                                    </p>
                                    <div class="row gy-2">
                                        <div class="col-6">
                                            <label class="form-label text-xs text-secondary-light mb-4">Min</label>
                                            <div class="input-group">
                                                <input type="text" inputmode="numeric" name="ppm_min"
                                                    value="{{ old('ppm_min') }}"
                                                    class="form-control radius-8 integer-input @error('ppm_min') is-invalid @enderror"
                                                    placeholder="400" required>
                                                <span class="input-group-text text-xs">ppm</span>
                                            </div>
                                            @error('ppm_min')
                                                <span class="text-danger-main text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-xs text-secondary-light mb-4">Max</label>
                                            <div class="input-group">
                                                <input type="text" inputmode="numeric" name="ppm_max"
                                                    value="{{ old('ppm_max') }}"
                                                    class="form-control radius-8 integer-input @error('ppm_max') is-invalid @enderror"
                                                    placeholder="800" required>
                                                <span class="input-group-text text-xs">ppm</span>
                                            </div>
                                            @error('ppm_max')
                                                <span class="text-danger-main text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 radius-8">
                                    <iconify-icon icon="solar:diskette-outline" class="me-2"></iconify-icon>
                                    Simpan Preset Baru
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════
             TAB 3 — INPUT MANUAL (termasuk ketinggian air)
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="tab-manual" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-xxl-8">
                    <div class="card">
                        <div class="card-body p-32">
                            <div class="d-flex align-items-center gap-3 mb-32">
                                <div
                                    class="w-48-px h-48-px bg-primary-100 text-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="solar:pen-outline" class="text-xl"></iconify-icon>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-4">Pengaturan Manual Threshold Sensor</h5>
                                    <p class="text-secondary-light text-sm mb-0">Edit langsung semua parameter termasuk
                                        Ketinggian Air Tandon.</p>
                                </div>
                            </div>

                            <form action="{{ route('configs.update') }}" method="POST" class="needs-decimal-fix">
                                @csrf

                                @php $cfg = $configs['ketinggian_air'] ?? null; @endphp
                                <div class="border radius-12 p-24 mb-24">
                                    <div class="d-flex align-items-center gap-3 mb-20">
                                        <div
                                            class="w-40-px h-40-px bg-primary-focus text-primary-main rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="mdi:water-pump" class="text-lg"></iconify-icon>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Ketinggian Air Tandon</h6>
                                            <p class="text-xs text-secondary-light mb-0">Batas ketinggian air tandon (kontrol pompa isi)
                                                @if ($cfg && $cfg->unit)
                                                    · Satuan: <strong>{{ $cfg->unit }}</strong>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row gy-3">
                                        <div class="col-sm-6">
                                            <label
                                                class="form-label text-sm fw-medium text-secondary-light text-uppercase">Batas Minimum</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    inputmode="decimal"
                                                    name="ketinggian_air_min"
                                                    value="{{ $cfg ? number_format($cfg->min_optimal, 2, '.', '') : old('ketinggian_air_min') }}"
                                                    class="form-control radius-8 decimal-input @error('ketinggian_air_min') is-invalid @enderror"
                                                    placeholder="Minimum" required>
                                                <span class="input-group-text text-secondary-light">cm</span>
                                            </div>
                                            @error('ketinggian_air_min')
                                                <span class="text-danger-main text-xs d-block mt-4">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <label
                                                class="form-label text-sm fw-medium text-secondary-light text-uppercase">Batas Maksimum</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    inputmode="decimal"
                                                    name="ketinggian_air_max"
                                                    value="{{ $cfg ? number_format($cfg->max_optimal, 2, '.', '') : old('ketinggian_air_max') }}"
                                                    class="form-control radius-8 decimal-input @error('ketinggian_air_max') is-invalid @enderror"
                                                    placeholder="Maksimum" required>
                                                <span class="input-group-text text-secondary-light">cm</span>
                                            </div>
                                            @error('ketinggian_air_max')
                                                <span class="text-danger-main text-xs d-block mt-4">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

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
        </div>

    </div>{{-- end tab-content --}}

    {{-- ── Modal Edit Preset ─────────────────────────────────────────────────── --}}
    <div class="modal fade" id="editPresetModal" tabindex="-1" aria-labelledby="editPresetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content radius-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPresetModalLabel">Edit Preset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPreset" method="POST" action="" class="needs-decimal-fix">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-24">
                        <div class="mb-16">
                            <label class="form-label text-sm fw-medium">Nama Preset <span class="text-danger-main">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control radius-8" required>
                        </div>
                        <div class="mb-16">
                            <label class="form-label text-sm fw-medium">Deskripsi</label>
                            <textarea name="description" id="edit_description" rows="2" class="form-control radius-8"></textarea>
                        </div>
                        
                        <div class="row gy-3 mb-16 border-top pt-16 mt-8">
                            <div class="col-12"><h6 class="text-secondary-light text-xs mb-0">pH Air</h6></div>
                            <div class="col-6">
                                <label class="form-label text-xs">Min</label>
                                <input type="text" name="ph_min" id="edit_ph_min" class="form-control radius-8 decimal-input" inputmode="decimal" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-xs">Max</label>
                                <input type="text" name="ph_max" id="edit_ph_max" class="form-control radius-8 decimal-input" inputmode="decimal" required>
                            </div>
                        </div>

                        <div class="row gy-3 mb-16 border-top pt-16 mt-8">
                            <div class="col-12"><h6 class="text-secondary-light text-xs mb-0">Suhu Air (°C)</h6></div>
                            <div class="col-6">
                                <label class="form-label text-xs">Min</label>
                                <input type="text" name="suhu_min" id="edit_suhu_min" class="form-control radius-8 decimal-input" inputmode="decimal" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-xs">Max</label>
                                <input type="text" name="suhu_max" id="edit_suhu_max" class="form-control radius-8 decimal-input" inputmode="decimal" required>
                            </div>
                        </div>

                        <div class="row gy-3 mb-16 border-top pt-16 mt-8">
                            <div class="col-12"><h6 class="text-secondary-light text-xs mb-0">PPM Nutrisi</h6></div>
                            <div class="col-6">
                                <label class="form-label text-xs">Min</label>
                                <input type="text" name="ppm_min" id="edit_ppm_min" class="form-control radius-8 integer-input" inputmode="numeric" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-xs">Max</label>
                                <input type="text" name="ppm_max" id="edit_ppm_max" class="form-control radius-8 integer-input" inputmode="numeric" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary radius-8">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Modal Edit Handler ────────────────────────────────────────────────────
        document.querySelectorAll('.btn-edit-preset').forEach(btn => {
            btn.addEventListener('click', function() {
                const preset = JSON.parse(this.dataset.preset);
                const form = document.getElementById('formEditPreset');
                
                // Format action URL
                const baseUrl = '{{ route("configs.preset.update", 0) }}'.replace('/0', '/' + preset.id);
                form.action = baseUrl;
                
                // Isi input fields
                document.getElementById('edit_name').value = preset.name;
                document.getElementById('edit_description').value = preset.description || '';
                
                // Parsing numbers
                document.getElementById('edit_ph_min').value = parseFloat(preset.ph_min).toString();
                document.getElementById('edit_ph_max').value = parseFloat(preset.ph_max).toString();
                document.getElementById('edit_suhu_min').value = parseFloat(preset.suhu_min).toString();
                document.getElementById('edit_suhu_max').value = parseFloat(preset.suhu_max).toString();
                document.getElementById('edit_ppm_min').value = parseFloat(preset.ppm_min).toString();
                document.getElementById('edit_ppm_max').value = parseFloat(preset.ppm_max).toString();
            });
        });

        // ── Aktifkan tab sesuai URL fragment ──────────────────────────────────────
        (function() {
            const hash = window.location.hash;
            if (hash) {
                const btn = document.querySelector('[data-bs-target="' + hash + '"]');
                if (btn) new bootstrap.Tab(btn).show();
            }
        })();

        // ── Hover effect preset card ──────────────────────────────────────────────
        document.querySelectorAll('.preset-card').forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                card.style.boxShadow = '0 8px 24px rgba(46,125,50,.14)';
                card.style.borderColor = '#4caf50';
            });
            card.addEventListener('mouseleave', function() {
                card.style.boxShadow = '';
                card.style.borderColor = '';
            });
        });

        // ── Validasi & normalisasi input desimal ──────────────────────────────────
        // Ganti koma → titik, blok huruf non-numerik, tampilkan pesan jika invalid

        function normalizeDecimal(input) {
            // Ganti koma dengan titik (locale ID/EU)
            let val = input.value.replace(',', '.');
            // Hapus semua karakter selain angka dan titik
            val = val.replace(/[^0-9.]/g, '');
            // Pastikan hanya ada satu titik
            const parts = val.split('.');
            if (parts.length > 2) val = parts[0] + '.' + parts.slice(1).join('');
            input.value = val;
        }

        function normalizeInteger(input) {
            // Hanya angka
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        // Pasang event listener ke semua decimal-input dan integer-input
        document.querySelectorAll('.decimal-input').forEach(function(inp) {
            inp.addEventListener('input', function() {
                normalizeDecimal(this);
            });
            inp.addEventListener('blur', function() {
                normalizeDecimal(this);
                // Validasi range jika ada data-min/data-max
                const val = parseFloat(this.value);
                const min = this.dataset.min !== undefined ? parseFloat(this.dataset.min) : null;
                const max = this.dataset.max !== undefined ? parseFloat(this.dataset.max) : null;
                const feedback = this.parentElement.querySelector('.range-feedback') ||
                    (() => {
                        const el = document.createElement('span');
                        el.className = 'text-danger-main text-xs range-feedback d-block mt-4';
                        this.closest('.input-group, .col-6, .col-sm-6').appendChild(el);
                        return el;
                    })();
                if (!isNaN(val)) {
                    if (min !== null && val < min) {
                        feedback.textContent = 'Nilai minimal ' + min;
                    } else if (max !== null && val > max) {
                        feedback.textContent = 'Nilai maksimal ' + max;
                    } else {
                        feedback.textContent = '';
                    }
                } else {
                    feedback.textContent = '';
                }
            });
        });

        document.querySelectorAll('.integer-input').forEach(function(inp) {
            inp.addEventListener('input', function() {
                normalizeInteger(this);
            });
        });

        // Normalisasi sebelum submit: pastikan semua koma sudah jadi titik
        document.querySelectorAll('form.needs-decimal-fix').forEach(function(form) {
            form.addEventListener('submit', function() {
                form.querySelectorAll('.decimal-input').forEach(normalizeDecimal);
                form.querySelectorAll('.integer-input').forEach(normalizeInteger);
            });
        });
    </script>
@endpush
