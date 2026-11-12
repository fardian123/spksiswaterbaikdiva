@php $s = $siswa ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">NIS <span class="text-danger">*</span></label>
        <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror"
               value="{{ old('nis', $s->nis ?? '') }}" required>
        @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
               value="{{ old('nama', $s->nama ?? '') }}" required>
        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Kelas</label>
        <input type="text" name="kelas" class="form-control" placeholder="XI TE 2"
               value="{{ old('kelas', $s->kelas ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Jurusan</label>
        <input type="text" name="jurusan" class="form-control" placeholder="Teknik Elektronika"
               value="{{ old('jurusan', $s->jurusan ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026"
               value="{{ old('tahun_ajaran', $s->tahun_ajaran ?? '') }}">
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
    <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>