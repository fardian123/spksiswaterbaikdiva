
@extends('layouts.app')

@section('title', 'Hasil AHP')

@section('content')
@php
    $kr = $detail['kriteria'];
    $n  = count($kr);
@endphp

<h4 class="mb-3">Hasil Perhitungan AHP</h4>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold">Matriks Perbandingan Berpasangan</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th></th>
                    @foreach ($kr as $k) <th>{{ $k['kode'] }}</th> @endforeach
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $n; $i++)
                    <tr>
                        <th class="table-light">{{ $kr[$i]['kode'] }}</th>
                        @for ($j = 0; $j < $n; $j++)
                            <td>{{ number_format($detail['matriks'][$i][$j], 4) }}</td>
                        @endfor
                    </tr>
                @endfor
                <tr class="table-light fw-bold">
                    <td>Jumlah</td>
                    @for ($j = 0; $j < $n; $j++)
                        <td>{{ number_format($detail['jumlah_kolom'][$j], 4) }}</td>
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold">Normalisasi & Bobot Prioritas</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th></th>
                    @foreach ($kr as $k) <th>{{ $k['kode'] }}</th> @endforeach
                    <th>Bobot</th>
                    <th>Bobot (%)</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $n; $i++)
                    <tr>
                        <th class="table-light">{{ $kr[$i]['kode'] }} - {{ $kr[$i]['nama'] }}</th>
                        @for ($j = 0; $j < $n; $j++)
                            <td>{{ number_format($detail['normalisasi'][$i][$j], 4) }}</td>
                        @endfor
                        <td>{{ number_format($detail['bobot'][$kr[$i]['id']], 4) }}</td>
                        <td class="fw-bold">{{ number_format($detail['bobot'][$kr[$i]['id']] * 100, 2) }}%</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold">Uji Konsistensi</div>
    <div class="card-body">
        <table class="table table-sm w-auto mb-3">
            <tr><td>Lambda max</td><td>{{ number_format($hasil->lambda_max, 4) }}</td></tr>
            <tr><td>CI</td><td>{{ number_format($hasil->ci, 4) }}</td></tr>
            <tr><td>RI (n = {{ $n }})</td><td>{{ number_format($hasil->ri, 2) }}</td></tr>
            <tr><td>CR</td><td>{{ number_format($hasil->cr, 4) }}</td></tr>
            <tr>
                <td>Status</td>
                <td>
                    @if ($hasil->is_konsisten)
                        <span class="badge bg-success">Konsisten (CR &le; 0,1)</span>
                    @else
                        <span class="badge bg-danger">Tidak konsisten (CR &gt; 0,1)</span>
                    @endif
                </td>
            </tr>
        </table>

        <a href="{{ route('ahp.index') }}" class="btn btn-outline-secondary">Ubah Perbandingan</a>

        @if ($hasil->is_konsisten)
            <form action="{{ route('ahp.terapkan', $hasil) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Terapkan bobot ini ke kriteria?')">
                @csrf
                <button class="btn btn-success">Terapkan Bobot ke Kriteria</button>
            </form>
        @endif

        @if ($hasil->diterapkan_at)
            <span class="text-success small ms-2">Diterapkan {{ $hasil->diterapkan_at->format('d/m/Y H:i') }}</span>
        @endif
    </div>
</div>
@endsection