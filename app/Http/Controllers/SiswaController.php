<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Tampilkan daftar siswa (dengan pencarian sederhana).
     */
    public function index(Request $request)
    {
        $keyword = $request->get('q');

        $siswa = Siswa::when($keyword, function ($query) use ($keyword) {
            $query->where('nama', 'like', "%{$keyword}%")
                ->orWhere('nis', 'like', "%{$keyword}%");
        })
            ->orderBy('nama')
            ->paginate(15);

        return view('siswa.index', compact('siswa', 'keyword'));
    }

    /**
     * Form tambah siswa baru.
     */
    public function create()
    {
        return view('siswa.create');
    }

    /**
     * Simpan siswa baru (input manual).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:siswa,nis',
            'nama' => 'required|string|max:150',
            'kelas' => 'nullable|string|max:50',
            'jurusan' => 'nullable|string|max:100',
            'tahun_ajaran' => 'nullable|string|max:20',
        ]);

        Siswa::create($validated);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Form edit siswa.
     */
    public function edit(Siswa $siswa)
    {
        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Update data siswa.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:siswa,nis,' . $siswa->id,
            'nama' => 'required|string|max:150',
            'kelas' => 'nullable|string|max:50',
            'jurusan' => 'nullable|string|max:100',
            'tahun_ajaran' => 'nullable|string|max:20',
        ]);

        $siswa->update($validated);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa (otomatis ikut menghapus nilai, absensi, dst karena cascade).
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
