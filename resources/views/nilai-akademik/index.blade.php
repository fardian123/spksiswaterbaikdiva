@extends('layouts.app')

@section('title', 'Nilai Akademik')

@section('content')
<h4 class="mb-3">Nilai Akademik (Rata-rata NR)</h4>

<div class="alert alert-info small">
    Data ini otomatis terisi saat import file ranking di menu
    <a href="{{ route('absensi.index') }}">Absensi</a>. Form di bawah untuk input / koreksi manual.
</div>

<div class="card border-0 shadow-sm">
    <form action="{{ route('nilai-akademik.storeManual') }}" method="POST">
        @csrf
        <div class="card-body">
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
                            <th width="220">Rata-rata NR</th>
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
                                           name="rata_rata_nr[{{ $s->id }}]" class="form-control form-control-sm"
                                           value="{{ old('rata_rata_nr.' . $s->id, optional($s->nilaiAkademik)->rata_rata_nr) }}">
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($siswa->count())
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
            @endif
        </div>
    </form>
    <div class="card-footer bg-white">{{ $siswa->links() }}</div>
</div>
@endsection