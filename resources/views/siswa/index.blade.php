@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Data Siswa</h4>
    <a href="{{ route('siswa.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Siswa
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Cari nama / NIS..." value="{{ $keyword }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
                @if ($keyword)
                    <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Tahun Ajaran</th>
                        <th width="140" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswa as $s)
                        <tr>
                            <td>{{ $siswa->firstItem() + $loop->index }}</td>
                            <td>{{ $s->nis }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->kelas ?? '-' }}</td>
                            <td>{{ $s->jurusan ?? '-' }}</td>
                            <td>{{ $s->tahun_ajaran ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('siswa.edit', $s) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('siswa.destroy', $s) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus siswa {{ $s->nama }}? Semua nilainya ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $siswa->links() }}
    </div>
</div>
@endsection