<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Absensi;
use App\Imports\RangkingImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    /**
     * Daftar siswa beserta rata-rata persentase kehadirannya (jika ada).
     */
    public function index()
    {
        $siswa = Siswa::with('absensi')->orderBy('nama')->paginate(15);

        return view('absensi.index', compact('siswa'));
    }

    /**
     * Simpan/perbarui absensi secara manual untuk banyak siswa sekaligus.
     * Form diharapkan mengirim:
     *   semester = string
     *   rata_rata[siswa_id] = angka persen
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'semester'      => 'required|string|max:30',
            'rata_rata'     => 'required|array',
            'rata_rata.*'   => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['rata_rata'] as $siswaId => $persen) {
            if ($persen === null || $persen === '') {
                continue;
            }

            Absensi::updateOrCreate(
                ['siswa_id' => $siswaId, 'semester' => $validated['semester']],
                ['rata_rata_persen' => $persen]
            );
        }

        return redirect()->route('absensi.index')
            ->with('success', 'Data absensi berhasil disimpan.');
    }

    /**
     * Import dari file ranking (format seperti Rangking_XI_TE_2_1b_2526_xls.xls)
     * yang berisi kolom "Rata-rata ABS" dan "Rata-rata NR".
     *
     * Catatan: import ini SEKALIGUS mengisi tabel nilai_akademik
     * (kriteria Akademik), karena kedua data tersebut berasal dari file
     * yang sama. Lihat App\Imports\RangkingImport.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file'     => 'required|mimes:xls,xlsx,csv',
            'semester' => 'required|string|max:30',
        ]);

        Excel::import(
            new RangkingImport($request->input('semester')),
            $request->file('file')
        );

        return redirect()->route('absensi.index')
            ->with('success', 'Import data absensi & nilai akademik berhasil.');
    }
}
