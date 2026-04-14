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
                    {{-- Image Placeholder --}}
                    <div class="position-relative" style="height:200px; overflow:hidden;">
                        @if ($anomaly->image_url)
                            <img src="{{ asset('storage/' . $anomaly->image_url) }}" alt="ESP32 Cam"
                                class="w-100 h-100 object-fit-cover">
                        @else
                            <div>
                                <iconify-icon icon="mdi:camera" class="text-secondary-light display-4"></iconify-icon>
                                <p class="text-xs text-secondary-light mt-8 mb-0">ESP32 Cam</p>
                            </div>
                        @endif

                        {{-- Severity badge --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            @if ($anomaly->label_hama == 'sehat')
                                <span class="badge bg-success rounded-pill">Tanaman Sehat</span>
                            @elseif($anomaly->label_hama == 'siput' || $anomaly->label_hama == 'ulat')
                                <span class="badge bg-danger rounded-pill">Terdeteksi Anomaly
                                    {{ $anomaly->label_hama }}</span>
                            @elseif($anomaly->label_hama == 'berlubang')
                                <span class="badge bg-warning rounded-pill">Berlubang</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Data Kosong</span>
                            @endif
                        </div>

                        @if ($anomaly->resolved_at)
                            <div class="position-absolute top-12 start-12">
                                <span class="badge bg-success-focus text-success-main rounded-pill">✓ Ditangani</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="card-body p-16">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-8">
                            <h6 class="fw-semibold mb-0">{{ str_replace('_', ' ', ucfirst($anomaly->type)) }}</h6>
                            <span
                                class="text-xs text-secondary-light flex-shrink-0">{{ $anomaly->created_at->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-xs text-secondary-light mb-12"></p>

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-8">
                                <span class="text-xs text-secondary-light">Kepercayaan:
                                    {{ number_format($anomaly->confidence * 100, 2) }}%</span>
                            </div>

                            {{-- @if (!$anomaly->resolved_at)
                                <form method="POST" action="{{ route('anomalies.resolve', $anomaly) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm radius-6 px-12">
                                    <iconify-icon icon="mdi:check" class="me-1"></iconify-icon>
                                    Tangani
                                </button>
                                </form>
                            @else
                                <span class="text-xs text-success-main fw-medium">Selesai</span>
                            @endif --}}
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
