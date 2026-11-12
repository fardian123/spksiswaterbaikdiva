@extends('layouts.app')

@section('title', 'Data Mapel')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Data Mapel</h4>
        <a href="{{ route('mapel.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Mapel
        </a>
    </div>

    @if ($mapel->whereNull('kategori')->count() > 0)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Ada mapel yang belum ditentukan kategorinya. Pilih <strong>Umum / Kejuruan / Kreatif</strong>
            lalu klik <strong>Simpan Kategori</strong>.
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <form action="{{ route('mapel.setKategori') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Kode</th>
                            <th>Nama Mapel</th>
                            <th width="220">Kategori</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mapel as $m)
                            <tr class="{{ $m->kategori ? '' : 'table-warning' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $m->kode_mapel ?? '-' }}</td>
                                <td>
                                    {{ $m->nama_mapel }}
                                    <input type="hidden" name="mapel_id[]" value="{{ $m->id }}">
                                </td>
                                <td>
                                    <select name="kategori[]" class="form-select form-select-sm">
                                        <option value="">-- Pilih kategori --</option>
                                        <option value="umum" @selected($m->kategori === 'umum')>Umum</option>
                                        <option value="kejuruan" @selected($m->kategori === 'kejuruan')>Kejuruan</option>
                                        <option value="kreatif" @selected($m->kategori === 'kreatif')>Kreatif</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('mapel.edit', $m) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="submit" form="hapus-mapel-{{ $m->id }}" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Hapus mapel {{ $m->nama_mapel }}? Nilainya ikut terhapus.')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada mapel. Tambah manual, atau import file nilai di menu Nilai Mapel.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($mapel->count())
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-circle me-1"></i>Simpan Kategori
                    </button>
                </div>
            @endif
        </form>
    </div>

    {{-- Form hapus dipisah di luar form kategori (form tidak boleh bersarang) --}}
    @foreach ($mapel as $m)
        <form id="hapus-mapel-{{ $m->id }}" action="{{ route('mapel.destroy', $m) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection