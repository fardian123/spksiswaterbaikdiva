<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\NilaiMapel;
use App\Imports\RekapNilaiImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NilaiController extends Controller
{
    /**
     * Daftar siswa, dengan info jumlah mapel yang sudah/belum diisi nilainya.
     */
    public function index()
    {
        $siswa = Siswa::withCount('nilaiMapel')->orderBy('nama')->paginate(15);
        $totalMapel = Mapel::count();

        return view('nilai.index', compact('siswa', 'totalMapel'));
    }

    /**
     * Form input nilai manual untuk satu siswa: menampilkan semua mapel
     * (dikelompokkan per kategori) beserta nilai yang sudah ada (jika ada).
     */
    public function edit(Siswa $siswa)
    {
        $mapelUmum     = Mapel::where('kategori', 'umum')->orderBy('nama_mapel')->get();
        $mapelKejuruan = Mapel::where('kategori', 'kejuruan')->orderBy('nama_mapel')->get();
        $mapelKreatif  = Mapel::where('kategori', 'kreatif')->orderBy('nama_mapel')->get();

        // nilai yang sudah ada, di-index by mapel_id supaya gampang dicek di view
        $nilaiSekarang = NilaiMapel::where('siswa_id', $siswa->id)
            ->pluck('nilai', 'mapel_id');

        return view('nilai.edit', compact(
            'siswa', 'mapelUmum', 'mapelKejuruan', 'mapelKreatif', 'nilaiSekarang'
        ));
    }

    /**
     * Simpan nilai manual untuk satu siswa, sekali submit untuk semua mapel.
     * Form diharapkan mengirim: nilai[mapel_id] = angka, semester = string
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'semester'    => 'required|string|max:30',
            'nilai'       => 'required|array',
            'nilai.*'     => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['nilai'] as $mapelId => $nilai) {
            if ($nilai === null || $nilai === '') {
                continue;
            }

            NilaiMapel::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'mapel_id' => $mapelId,
                    'semester' => $validated['semester'],
                ],
                ['nilai' => $nilai]
            );
        }

        return redirect()->route('nilai.index')
            ->with('success', "Nilai untuk {$siswa->nama} berhasil disimpan.");
    }

    /**
     * Import nilai dari file excel rekap nilai (format seperti
     * rekapnilk3_XI_TE_2_1b_2526.xls).
     *
     * Kolom mapel yang belum dikenal otomatis dibuat sebagai Mapel baru
     * dengan kategori kosong -> admin wajib atur kategorinya di menu Mapel
     * setelah proses import ini selesai.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file'     => 'required|mimes:xls,xlsx,csv',
            'semester' => 'required|string|max:30',
        ]);

        Excel::import(
            new RekapNilaiImport($request->input('semester')),
            $request->file('file')
        );

        return redirect()->route('nilai.index')
            ->with('success', 'Import nilai mapel berhasil. Silakan cek/atur kategori mapel di menu Mapel.');
    }
}
