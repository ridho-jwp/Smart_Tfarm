@extends('layouts.app')
@section('title', 'Deteksi Hama Daun — Smart Pakcoy')

@section('content')

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Deteksi Hama Daun</h6>
            <p class="text-xs text-secondary-light mt-4 mb-0">Gambar tangkapan ESP32 Cam dan hasil analisis hama</p>
        </div>
        <div class="d-flex align-items-center gap-8">
            <a href="{{ route('anomalies', ['filter' => 'all']) }}"
                class="btn btn-sm radius-8 {{ !request('filter') || request('filter') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Semua
            </a>
            <a href="{{ route('anomalies', ['filter' => 'unresolved']) }}"
                class="btn btn-sm radius-8 {{ request('filter') === 'unresolved' ? 'btn-danger' : 'btn-outline-secondary' }}">
                Belum Ditangani
            </a>
            <a href="{{ route('anomalies', ['filter' => 'resolved']) }}"
                class="btn btn-sm radius-8 {{ request('filter') === 'resolved' ? 'btn-success' : 'btn-outline-secondary' }}">
                Selesai
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 gy-4">
        @forelse($anomalies as $anomaly)
            <div class="col">
                <div class="card h-100 overflow-hidden">
                    {{-- Image Placeholder --}}
                    <div class="position-relative" style="height:200px; overflow:hidden;">
                        @if($anomaly->image_path)
                            <img src="{{ asset('storage/' . $anomaly->image_path) }}" alt="ESP32 Cam"
                                class="w-100 h-100 object-fit-cover">
                        @else
                            <div
                                class="w-100 h-100 d-flex flex-column align-items-center justify-content-center
                                {{ $anomaly->type === 'normal' ? 'bg-success-focus' : ($anomaly->severity === 'high' ? 'bg-danger-focus' : 'bg-warning-focus') }}">
                                @if($anomaly->type === 'normal')
                                    <iconify-icon icon="mdi:leaf" class="text-success-main display-4"></iconify-icon>
                                    <p class="text-xs text-success-main mt-8 mb-0 fw-medium">Tanaman Sehat</p>
                                @elseif($anomaly->type === 'bercak_daun')
                                    <iconify-icon icon="mdi:alert-circle" class="text-danger-main display-4"></iconify-icon>
                                    <p class="text-xs text-danger-main mt-8 mb-0 fw-medium">Bercak Daun</p>
                                @elseif($anomaly->type === 'daun_kuning')
                                    <iconify-icon icon="mdi:alert" class="text-warning-main display-4"></iconify-icon>
                                    <p class="text-xs text-warning-main mt-8 mb-0 fw-medium">Daun Menguning</p>
                                @elseif($anomaly->type === 'akar_busuk')
                                    <iconify-icon icon="mdi:close-circle" class="text-danger-main display-4"></iconify-icon>
                                    <p class="text-xs text-danger-main mt-8 mb-0 fw-medium">Akar Busuk</p>
                                @elseif($anomaly->type === 'hama_kutu')
                                    <iconify-icon icon="mdi:bug" class="text-danger-main display-4"></iconify-icon>
                                    <p class="text-xs text-danger-main mt-8 mb-0 fw-medium">Hama Kutu Daun</p>
                                @elseif($anomaly->type === 'layu')
                                    <iconify-icon icon="mdi:weather-windy" class="text-warning-main display-4"></iconify-icon>
                                    <p class="text-xs text-warning-main mt-8 mb-0 fw-medium">Tanaman Layu</p>
                                @else
                                    <iconify-icon icon="mdi:camera" class="text-secondary-light display-4"></iconify-icon>
                                    <p class="text-xs text-secondary-light mt-8 mb-0">ESP32 Cam</p>
                                @endif
                            </div>
                        @endif

                        {{-- Severity badge --}}
                        <div class="position-absolute top-12 end-12">
                            @if($anomaly->severity === 'high')
                                <span class="badge bg-danger rounded-pill">Tinggi</span>
                            @elseif($anomaly->severity === 'medium')
                                <span class="badge bg-warning rounded-pill">Sedang</span>
                            @else
                                <span class="badge bg-success rounded-pill">Rendah</span>
                            @endif
                        </div>

                        @if($anomaly->resolved_at)
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
                        <p class="text-xs text-secondary-light mb-12">{{ $anomaly->description }}</p>

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-8">
                                <span class="text-xs text-secondary-light">Kepercayaan:</span>
                                <div class="progress" style="width:60px; height:6px;">
                                    <div class="progress-bar {{ $anomaly->confidence >= 0.8 ? 'bg-success-main' : ($anomaly->confidence >= 0.6 ? 'bg-warning-main' : 'bg-danger-main') }} rounded-pill"
                                        style="width: {{ $anomaly->confidence * 100 }}%"></div>
                                </div>
                                <span
                                    class="text-xs fw-semibold {{ $anomaly->confidence >= 0.8 ? 'text-success-main' : 'text-warning-main' }}">{{ round($anomaly->confidence * 100) }}%</span>
                            </div>

                            @if(!$anomaly->resolved_at)
                                <form method="POST" action="{{ route('anomalies.resolve', $anomaly) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm radius-6 px-12">
                                        <iconify-icon icon="mdi:check" class="me-1"></iconify-icon>
                                        Tangani
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-success-main fw-medium">Selesai</span>
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
    @if($anomalies->hasPages())
        <div class="d-flex justify-content-center mt-24">
            {{ $anomalies->withQueryString()->links() }}
        </div>
    @endif

@endsection