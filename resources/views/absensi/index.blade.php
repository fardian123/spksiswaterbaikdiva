@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<h4 class="mb-3">Absensi (Rata-rata Kehadiran %)</h4>

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
            <form action="{{ route('absensi.storeManual') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                    <input type="text" name="semester" class="form-control w-auto"
                           value="{{ old('semester', 'Genap 2025/2026') }}" required>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="60">No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th width="220">Rata-rata Kehadiran (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswa as $s)
                                <tr>
                                    <td>{{ $siswa->firstItem() + $loop->index }}</td>
                                    <td>{{ $s->nis }}</td>
                                    <td>{{ $s->nama }}</td>
                                    <td>
                                        <input type="number" step="0.001" min="0" max="100"
                                               name="rata_rata[{{ $s->id }}]" class="form-control form-control-sm"
                                               value="{{ old('rata_rata.' . $s->id, optional($s->absensi)->rata_rata_persen) }}">
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada siswa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($siswa->count())
                    <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Absensi</button>
                    <span class="text-muted small ms-2">Simpan sebelum pindah halaman pagination.</span>
                @endif
            </form>

            <div class="mt-3">{{ $siswa->links() }}</div>
        </div>

        {{-- TAB IMPORT EXCEL --}}
        <div class="tab-pane fade" id="tab-import">
            <div class="alert alert-info small">
                Upload file ranking (contoh: <code>Rangking_XI_TE_2_1b_2526_xls.xls</code>).
                Satu file ini mengisi <strong>Absensi</strong> (kolom Rata-rata ABS) sekaligus
                <strong>Nilai Akademik</strong> (kolom Rata-rata NR).
            </div>

            <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data" class="row g-3">
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