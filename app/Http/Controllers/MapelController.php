<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    /**
     * Daftar semua mapel, dikelompokkan per kategori.
     * Mapel yang kategori-nya masih null akan tampil di bagian
     * "Belum Dikategorikan" agar admin langsung menentukannya.
     */
    public function index()
    {
        $mapel = Mapel::orderByRaw("kategori IS NULL DESC")
            ->orderBy('kategori')
            ->orderBy('nama_mapel')
            ->get();

        return view('mapel.index', compact('mapel'));
    }

    /**
     * Form tambah mapel manual (di luar hasil import excel).
     */
    public function create()
    {
        return view('mapel.create');
    }

    /**
     * Simpan mapel baru secara manual.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel'  => 'nullable|string|max:30',
            'nama_mapel'  => 'required|string|max:150|unique:mapel,nama_mapel',
            'kategori'    => 'nullable|in:umum,kejuruan,kreatif',
        ]);

        Mapel::create($validated);

        return redirect()->route('mapel.index')
            ->with('success', 'Mapel baru berhasil ditambahkan.');
    }

    /**
     * Form edit satu mapel (misalnya membenarkan nama yang
     * terpotong hasil import excel: "Sistem Kendali Elekt" -> lengkap).
     */
    public function edit(Mapel $mapel)
    {
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $validated = $request->validate([
            'kode_mapel'  => 'nullable|string|max:30',
            'nama_mapel'  => 'required|string|max:150|unique:mapel,nama_mapel,' . $mapel->id,
            'kategori'    => 'nullable|in:umum,kejuruan,kreatif',
        ]);

        $mapel->update($validated);

        return redirect()->route('mapel.index')
            ->with('success', 'Mapel berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return redirect()->route('mapel.index')
            ->with('success', 'Mapel berhasil dihapus.');
    }

    /**
     * Simpan kategori untuk banyak mapel sekaligus.
     * Dipanggil dari form di halaman index yang berisi
     * select kategori (umum/kejuruan/kreatif) per baris mapel.
     *
     * Form view diharapkan mengirim:
     *   mapel_id[]  = [1, 2, 3, ...]
     *   kategori[]  = ['umum', 'kejuruan', 'kreatif', ...]
     * dengan index array yang selaras (mapel_id[0] berpasangan dengan kategori[0]).
     */
    public function setKategori(Request $request)
    {
        $validated = $request->validate([
            'mapel_id'   => 'required|array',
            'mapel_id.*' => 'exists:mapel,id',
            'kategori'   => 'required|array',
            'kategori.*' => 'in:umum,kejuruan,kreatif',
        ]);

        foreach ($validated['mapel_id'] as $index => $mapelId) {
            Mapel::where('id', $mapelId)->update([
                'kategori' => $validated['kategori'][$index],
            ]);
        }

        return redirect()->route('mapel.index')
            ->with('success', 'Kategori mapel berhasil disimpan.');
    }
}