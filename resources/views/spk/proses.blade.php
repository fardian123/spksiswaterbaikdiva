@extends('layouts.app')

@section('title', 'Proses SPK')

@section('content')
<h4 class="mb-3">Proses Perhitungan SPK (Metode SAW)</h4>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Kriteria yang Digunakan</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Kode</th><th>Kriteria</th><th>Atribut</th><th class="text-end">Bobot</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteria as $k)
                            <tr>
                                <td>{{ $k->kode }}</td>
                                <td>{{ $k->nama_kriteria }}</td>
                                <td class="text-capitalize">{{ $k->atribut }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format($k->bobot, 2), '0'), '.') }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end {{ (float) $totalBobot == 100.0 ? 'text-success' : 'text-danger' }}">
                                {{ rtrim(rtrim(number_format($totalBobot, 2), '0'), '.') }}%
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Jalankan Proses</div>
            <div class="card-body">
                @if ((float) $totalBobot != 100.0)
                    <div class="alert alert-danger small">
                        Total bobot belum 100%. Perbaiki dulu di menu
                        <a href="{{ route('kriteria.index') }}">Kriteria & Bobot</a>.
                    </div>
                @endif

                <form action="{{ route('spk.proses') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <input type="text" name="semester" class="form-control"
                               value="{{ old('semester', 'Genap 2025/2026') }}" required>
                        <div class="form-text">Harus sama persis dengan semester yang dipakai saat input / import data.</div>
                    </div>
                    <button class="btn btn-primary w-100" {{ (float) $totalBobot != 100.0 ? 'disabled' : '' }}>
                        <i class="bi bi-calculator me-1"></i>Hitung Ranking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection