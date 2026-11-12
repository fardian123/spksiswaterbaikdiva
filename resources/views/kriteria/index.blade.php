@extends('layouts.app')

@section('title', 'Kriteria & Bobot')

@section('content')
<h4 class="mb-3">Kriteria & Bobot</h4>

<div class="card border-0 shadow-sm">
   <form action="{{ route('kriteria.updateAtribut') }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Kode</th>
                        <th>Nama Kriteria</th>
                        <th width="180">Atribut</th>
                        <th width="180">Bobot (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kriteria as $k)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $k->kode }}</span></td>
                            <td>{{ $k->nama_kriteria }}</td>
                            <td>
                                <select name="atribut[{{ $k->id }}]" class="form-select form-select-sm">
                                    <option value="benefit" @selected(old('atribut.' . $k->id, $k->atribut) === 'benefit')>Benefit</option>
                                    <option value="cost" @selected(old('atribut.' . $k->id, $k->atribut) === 'cost')>Cost</option>
                                </select>
                            </td>
                           <td>
                                <span class="fw-bold">{{ number_format($k->bobot, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Data kriteria kosong. Jalankan <code>php artisan db:seed</code>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total Bobot</th>
                        <th>{{ number_format($kriteria->sum('bobot'), 2) }}%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer bg-white">
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Atribut</button>
            <a href="{{ route('ahp.index') }}" class="btn btn-outline-success ms-2">
                <i class="bi bi-diagram-3 me-1"></i>Tentukan Bobot dengan AHP
            </a>
        </div>
    </form>
</div>
@endsection
