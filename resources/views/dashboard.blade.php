@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h4 class="mb-4">Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Siswa</div>
                <div class="fs-3 fw-bold">{{ $totalSiswa }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Mapel</div>
                <div class="fs-3 fw-bold">{{ $totalMapel }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Umum / Kejuruan / Kreatif</div>
                <div class="fs-5 fw-bold">{{ $totalUmum }} / {{ $totalKejuruan }} / {{ $totalKreatif }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm {{ $belumDikategori > 0 ? 'border-warning' : '' }}">
            <div class="card-body">
                <div class="text-muted small">Mapel Belum Dikategori</div>
                <div class="fs-3 fw-bold {{ $belumDikategori > 0 ? 'text-warning' : '' }}">{{ $belumDikategori }}</div>
                @if ($belumDikategori > 0)
                    <a href="{{ route('mapel.index') }}" class="small">Atur sekarang</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Top 5 Siswa Terbaik</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">Rank</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th class="text-end">Skor Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($top5 as $h)
                    <tr>
                        <td><span class="badge bg-primary">{{ $h->ranking }}</span></td>
                        <td>{{ $h->siswa->nis }}</td>
                        <td>{{ $h->siswa->nama }}</td>
                        <td class="text-end">{{ number_format($h->skor_akhir, 4) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada hasil. Jalankan <a href="{{ route('spk.formProses') }}">Proses SPK</a> terlebih dahulu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection