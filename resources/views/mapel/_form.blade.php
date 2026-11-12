@php $m = $mapel ?? null; @endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Kode Mapel</label>
        <input type="text" name="kode_mapel" class="form-control"
               value="{{ old('kode_mapel', $m->kode_mapel ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Nama Mapel <span class="text-danger">*</span></label>
        <input type="text" name="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror"
               value="{{ old('nama_mapel', $m->nama_mapel ?? '') }}" required>
        @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Kategori</label>
        @php $kat = old('kategori', $m->kategori ?? ''); @endphp
        <select name="kategori" class="form-select">
            <option value="">-- Belum ditentukan --</option>
            <option value="umum" @selected($kat === 'umum')>Umum</option>
            <option value="kejuruan" @selected($kat === 'kejuruan')>Kejuruan</option>
            <option value="kreatif" @selected($kat === 'kreatif')>Kreatif</option>
        </select>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
    <a href="{{ route('mapel.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>