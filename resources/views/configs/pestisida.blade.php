@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-32">

        {{-- Header --}}
        <div class="mb-24">
            <h5 class="fw-bold">Input Dosis Pestisida</h5>
            <p class="text-secondary-light">
                Preset: <strong>{{ $preset->name }}</strong>
            </p>
        </div>

        {{-- Form --}}
        <form action="{{ route('pestisida.store', $preset->id) }}" method="POST">
            @csrf
            <input type="hidden" name="id_preset" value="{{ $preset->id }}">
            <div class="row gy-3">
                {{-- Dosis --}}
                <div class="col-md-3">
                    <label class="form-label">Dosis</label>
                    <input type="number" step="0.01" name="dosis" class="form-control" placeholder="Contoh: 2.5" min="1" required>
                </div>

                {{-- Satuan --}}
                <div class="col-md-3">
                    <label class="form-label">Satuan</label>
                        <p option value="ml/L" class="form-control">mL</p>
                    </select>
                </div>

                {{-- Keterangan --}}
                <div class="col-12">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>

                {{-- Tombol --}}
                <div class="col-12 d-flex gap-3 mt-3">
                    <a href="{{route('configs.index')}}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Dosis
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection