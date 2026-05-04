@extends('layouts.app')
@section('title', 'Deteksi Hama Daun — Smart Pakcoy')

@section('content')

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Deteksi Hama Daun</h6>
            <p class="text-xs text-secondary-light mt-4 mb-0">Gambar tangkapan ESP32 Cam dan hasil analisis hama</p>
        </div>
        <div class="d-flex align-items-center gap-8">
            <a href=""
                class="btn btn-sm radius-8 {{ !request('filter') || request('filter') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Semua
            </a>
            <a href=""
                class="btn btn-sm radius-8 {{ request('filter') === 'unresolved' ? 'btn-danger' : 'btn-outline-secondary' }}">
                Belum Ditangani
            </a>
            <a href=""
                class="btn btn-sm radius-8 {{ request('filter') === 'resolved' ? 'btn-success' : 'btn-outline-secondary' }}">
                Selesai
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 gy-4">
        @forelse($data as $anomaly)
            <div class="col">
                <div class="card h-100 overflow-hidden">
                    {{-- Image --}}
                    <div class="position-relative" style="height:200px; overflow:hidden; background:#f1f1f1; display:flex; align-items:center; justify-content:center;">
                        @if ($anomaly->image_url)
                            <img src="{{ $anomaly->image_url }}" alt="Hasil Deteksi"
                                class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="text-center">
                                <iconify-icon icon="mdi:camera" class="text-secondary-light display-4"></iconify-icon>
                                <p class="text-xs text-secondary-light mt-8 mb-0">Belum ada gambar</p>
                            </div>
                        @endif

                        {{-- Label badge --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            @if ($anomaly->label_hama == 'sehat')
                                <span class="badge bg-success rounded-pill">Sehat</span>
                            @elseif(in_array($anomaly->label_hama, ['ulat', 'siput']))
                                <span class="badge bg-danger rounded-pill">Hama: {{ ucfirst($anomaly->label_hama) }}</span>
                            @elseif($anomaly->label_hama == 'berlubang')
                                <span class="badge bg-warning rounded-pill">Berlubang</span>
                            @elseif($anomaly->label_hama == 'tidak terdeteksi')
                                <span class="badge bg-secondary rounded-pill">Tidak Terdeteksi</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">{{ $anomaly->label_hama ?? 'N/A' }}</span>
                            @endif
                        </div>

                        {{-- Pestisida badge --}}
                        @if ($anomaly->is_pestisida_pump)
                            <div class="position-absolute top-12 start-12">
                                <span class="badge bg-danger-focus text-danger-main rounded-pill">💧 Pompa Aktif</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="card-body p-16">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-8">
                            <h6 class="fw-semibold mb-0">{{ ucfirst($anomaly->label_hama ?? 'Tidak Diketahui') }}</h6>
                            <span class="text-xs text-secondary-light flex-shrink-0">{{ $anomaly->created_at->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-xs text-secondary-light mb-12">
                            {{ $anomaly->is_pestisida_pump ? 'Pompa pestisida dinyalakan otomatis.' : 'Tidak ada aksi otomatis.' }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-xs text-secondary-light">
                                Kepercayaan: <strong>{{ number_format($anomaly->confidence * 100, 2) }}%</strong>
                            </span>
                            @if($anomaly->is_pestisida_pump)
                                <span class="badge bg-danger-focus text-danger-main text-xs">Pompa ON</span>
                            @else
                                <span class="badge bg-success-focus text-success-main text-xs">Aman</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center py-48">
                    <div class="card-body">
                        <iconify-icon icon="mdi:camera-off-outline" class="text-neutral-300 display-3"></iconify-icon>
                        <p class="text-secondary-light mt-16 mb-0">Belum ada data deteksi hama dari ESP32 Cam.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    {{-- @if ($anomalies->hasPages())
        <div class="d-flex justify-content-center mt-24">
            {{ $anomalies->withQueryString()->links() }}
        </div>
    @endif --}}

@endsection
