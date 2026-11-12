@extends('layouts.app')

@section('title', 'Hasil SPK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Hasil Ranking Siswa Terbaik</h4>
    @if ($hasil->count())
        <a href="{{ route('spk.cetakPdf', ['semester' => $semester]) }}" target="_blank" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i>Cetak PDF
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="semester" class="form-control" placeholder="Filter semester (opsional)"
                       value="{{ $semester }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('spk.formProses') }}" class="btn btn-outline-secondary">Hitung Ulang</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="70">Rank</th>
                        <th>NIS</th>
                        <th class="text-start">Nama</th>
                        <th>Akademik</th>
                        <th>Umum</th>
                        <th>Kejuruan</th>
                        <th>Absensi</th>
                        <th>Kreatif</th>
                        <th>Skor Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hasil as $h)
                        <tr class="{{ $h->ranking == 1 ? 'table-success fw-bold' : '' }}">
                            <td class="text-center">
                                @if ($h->ranking <= 3)
                                    <span class="badge bg-{{ ['', 'warning text-dark', 'secondary', 'danger'][$h->ranking] }}">
                                        {{ $h->ranking }}
                                    </span>
                                @else
                                    {{ $h->ranking }}
                                @endif
                            </td>
                            <td>{{ $h->siswa->nis }}</td>
                            <td>{{ $h->siswa->nama }}</td>
                            <td class="text-center">{{ number_format($h->nilai_akademik, 2) }}</td>
                            <td class="text-center">{{ number_format($h->nilai_umum, 2) }}</td>
                            <td class="text-center">{{ number_format($h->nilai_kejuruan, 2) }}</td>
                            <td class="text-center">{{ number_format($h->nilai_absensi, 2) }}</td>
                            <td class="text-center">{{ number_format($h->nilai_kreatif, 2) }}</td>
                            <td class="text-center">{{ number_format($h->skor_akhir, 4) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Belum ada hasil. Jalankan <a href="{{ route('spk.formProses') }}">Proses SPK</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection