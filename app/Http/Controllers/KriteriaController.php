<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KriteriaController extends Controller
{
    /**
     * Tampilkan 5 kriteria beserta bobot & atribut (benefit/cost) masing-masing.
     */
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode')->get();

        return view('kriteria.index', compact('kriteria'));
    }

    /**
     * Perbarui bobot (dan atribut) semua kriteria sekaligus.
     * Form diharapkan mengirim:
     *   bobot[kriteria_id] = angka (persen)
     *   atribut[kriteria_id] = 'benefit' | 'cost'
     *
     * Total bobot WAJIB 100, kalau tidak akan ditolak dengan pesan error.
     */
    public function updateAtribut(Request $request)
    {
        $validated = $request->validate([
            'atribut' => 'required|array',
            'atribut.*' => 'required|in:benefit,cost',
        ]);

        foreach ($validated['atribut'] as $kriteriaId => $atribut) {
            Kriteria::where('id', $kriteriaId)->update(['atribut' => $atribut]);
        }

        return redirect()->route('kriteria.index')
            ->with('success', 'Atribut kriteria berhasil diperbarui.');
    }
}
