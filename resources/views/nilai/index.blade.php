@extends('layouts.app')

@section('title', 'Nilai Mapel')

@section('content')
<h4 class="mb-3">Nilai Mapel (Umum, Kejuruan, Kreatif)</h4>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-manual" type="button">
                    <i class="bi bi-pencil-square me-1"></i>Input Manual
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-import" type="button">
                    <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body tab-content">

        {{-- TAB INPUT MANUAL --}}
        <div class="tab-pane fade show active" id="tab-manual">
            <p class="text-muted small">
                Pilih siswa untuk mengisi / mengubah nilai semua mapel. Pastikan kategori mapel sudah ditentukan di menu
                <a href="{{ route('mapel.index') }}">Data Mapel</a>.
            </p>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelengkapan Nilai</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $s)
                            <tr>
                                <td>{{ $siswa->firstItem() + $loop->index }}</td>
                                <td>{{ $s->nis }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>
                                    @if ($totalMapel > 0 && $s->nilai_mapel_count >= $totalMapel)
                                        <span class="badge bg-success">{{ $s->nilai_mapel_count }} / {{ $totalMapel }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $s->nilai_mapel_count }} / {{ $totalMapel }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('nilai.edit', $s) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i> Input
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $siswa->links() }}
        </div>

        {{-- TAB IMPORT EXCEL --}}
        <div class="tab-pane fade" id="tab-import">
            <div class="alert alert-info small">
                Upload file rekap nilai (contoh: <code>rekapnilk3_XI_TE_2_1b_2526.xls</code>).
                Siswa dan mapel akan dibuat otomatis. Setelah import, buka menu
                <a href="{{ route('mapel.index') }}">Data Mapel</a> untuk menentukan kategori
                (umum / kejuruan / kreatif) tiap mapel.
            </div>

            <form action="{{ route('nilai.import') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                    <input type="text" name="semester" class="form-control"
                           value="{{ old('semester', 'Genap 2025/2026') }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">File Excel (.xls / .xlsx / .csv) <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" accept=".xls,.xlsx,.csv" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-success"><i class="bi bi-upload me-1"></i>Import</button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection