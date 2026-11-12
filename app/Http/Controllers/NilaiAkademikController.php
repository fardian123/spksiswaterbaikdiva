<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\NilaiAkademik;
use Illuminate\Http\Request;

class NilaiAkademikController extends Controller
{
    /**
     * Daftar siswa beserta nilai rata-rata akademik (kriteria 1) jika ada.
     * Data ini biasanya sudah otomatis terisi dari import di menu Absensi
     * (karena satu file excel ranking berisi kolom Rata-rata ABS & Rata-rata NR),
     * tapi tetap disediakan input manual untuk koreksi/kasus khusus.
     */
    public function index()
    {
        $siswa = Siswa::with('nilaiAkademik')->orderBy('nama')->paginate(15);

        return view('nilai-akademik.index', compact('siswa'));
    }

    /**
     * Simpan/perbarui nilai akademik manual untuk banyak siswa sekaligus.
     * Form diharapkan mengirim:
     *   semester = string
     *   rata_rata_nr[siswa_id] = angka
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'semester'          => 'required|string|max:30',
            'rata_rata_nr'      => 'required|array',
            'rata_rata_nr.*'    => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['rata_rata_nr'] as $siswaId => $nilai) {
            if ($nilai === null || $nilai === '') {
                continue;
            }

            NilaiAkademik::updateOrCreate(
                ['siswa_id' => $siswaId, 'semester' => $validated['semester']],
                ['rata_rata_nr' => $nilai]
            );
        }

        return redirect()->route('nilai-akademik.index')
            ->with('success', 'Nilai akademik berhasil disimpan.');
    }
}