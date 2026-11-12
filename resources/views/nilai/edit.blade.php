@extends('layouts.app')

@section('title', 'Input Nilai ' . $siswa->nama)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Input Nilai</h4>
        <div class="text-muted">{{ $siswa->nis }} &middot; {{ $siswa->nama }}</div>
    </div>
    <a href="{{ route('nilai.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($mapelUmum->isEmpty() && $mapelKejuruan->isEmpty() && $mapelKreatif->isEmpty())
    <div class="alert alert-warning">
        Belum ada mapel yang dikategorikan. Atur dulu di <a href="{{ route('mapel.index') }}">Data Mapel</a>.
    </div>
@endif

<form action="{{ route('nilai.update', $siswa) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label">Semester <span class="text-danger">*</span></label>
            <input type="text" name="semester" class="form-control w-auto"
                   value="{{ old('semester', 'Genap 2025/2026') }}" required>
        </div>
    </div>

    @php
        $kelompok = [
            ['Mapel Umum', 'primary', $mapelUmum],
            ['Mapel Kejuruan', 'success', $mapelKejuruan],
            ['Mapel Kreatif', 'warning', $mapelKreatif],
        ];
    @endphp

    @foreach ($kelompok as [$judul, $warna, $daftar])
        @if ($daftar->isNotEmpty())
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-{{ $warna }} {{ $warna === 'warning' ? 'text-dark' : 'text-white' }} fw-semibold">
                    {{ $judul }}
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($daftar as $m)
                            <div class="col-md-4">
                                <label class="form-label small">{{ $m->nama_mapel }}</label>
                                <input type="number" step="0.01" min="0" max="100"
                                       name="nilai[{{ $m->id }}]" class="form-control"
                                       value="{{ old('nilai.' . $m->id, $nilaiSekarang[$m->id] ?? '') }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Nilai</button>
</form>
@endsection