
@extends('layouts.app')

@section('title', 'AHP - Bobot Kriteria')

@section('content')
<h4 class="mb-3">Perbandingan Kriteria (AHP)</h4>

<div class="card border-0 shadow-sm">
    <form action="{{ route('ahp.hitung') }}" method="POST">
        @csrf
        <div class="card-body">
            <p class="text-muted small">
                Pilih seberapa penting kriteria kiri dibanding kriteria kanan
                (skala Saaty 1-9). Nilai pecahan berarti kriteria kiri LEBIH TIDAK penting.
            </p>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kriteria A</th>
                            <th width="320">Perbandingan</th>
                            <th>Kriteria B</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriterias as $i => $a)
                            @foreach ($kriterias as $j => $b)
                                @if ($i < $j)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $a->kode }}</span> {{ $a->nama_kriteria }}</td>
                                        <td>
                                            <select name="perbandingan[{{ $a->id }}][{{ $b->id }}]" class="form-select form-select-sm">
                                                @foreach ($skalaOpsi as $val => $label)
                                                    <option value="{{ $val }}"
                                                        @selected((string) old('perbandingan.' . $a->id . '.' . $b->id, $nilaiForm[$a->id][$b->id]) === (string) $val)>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $b->kode }}</span> {{ $b->nama_kriteria }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <button class="btn btn-primary"><i class="bi bi-calculator me-1"></i>Hitung AHP</button>
            @if ($hasilTerakhir)
                <a href="{{ route('ahp.hasil', $hasilTerakhir) }}" class="btn btn-outline-secondary ms-2">Lihat Hasil Terakhir</a>
            @endif
        </div>
    </form>
</div>
@endsection
