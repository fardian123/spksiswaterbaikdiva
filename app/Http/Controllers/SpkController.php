<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\HasilSpk;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SpkController extends Controller
{
    /**
     * Tampilkan form untuk memulai proses SPK (pilih semester).
     */
    public function formProses()
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $totalBobot = $kriteria->sum('bobot');

        return view('spk.proses', compact('kriteria', 'totalBobot'));
    }

    /**
     * Jalankan perhitungan SPK dengan metode SAW (Simple Additive Weighting).
     *
     * Atribut benefit/cost mengikuti tabel kriteria (semakin besar semakin baik):
     * 1) Akademik        -> dari nilai_akademik.rata_rata_nr
     * 2) Nilai Umum      -> rata-rata nilai_mapel yang kategori = umum
     * 3) Nilai Kejuruan  -> rata-rata nilai_mapel yang kategori = kejuruan
     * 4) Absensi         -> dari absensi.rata_rata_persen
     * 5) Kreatif         -> rata-rata nilai_mapel yang kategori = kreatif
     */
    public function proses(Request $request)
    {
        $request->validate([
            'semester' => 'required|string|max:30',
        ]);

        $semester = $request->input('semester');

        $kriteria = Kriteria::all()->keyBy('kode');
        if (round($kriteria->sum('bobot'), 2) != 100) {
            return back()->with('error', 'Total bobot kriteria belum 100%. Hitung dulu bobot lewat menu AHP lalu terapkan.');
        }

        $siswaSemua = Siswa::with([
            'nilaiMapel' => function ($q) use ($semester) {
                $q->where('semester', $semester)->with('mapel');
            },
            'absensi' => function ($q) use ($semester) {
                $q->where('semester', $semester);
            },
            'nilaiAkademik' => function ($q) use ($semester) {
                $q->where('semester', $semester);
            },
        ])->get();

        if ($siswaSemua->isEmpty()) {
            return back()->with('error', 'Belum ada data siswa/nilai untuk semester ini.');
        }

        // 1) Kumpulkan nilai mentah per kriteria per siswa
        $dataMentah = [];

        foreach ($siswaSemua as $siswa) {
            $umum = $siswa->nilaiMapel
                ->filter(fn($n) => optional($n->mapel)->kategori === 'umum')
                ->avg('nilai');

            $kejuruan = $siswa->nilaiMapel
                ->filter(fn($n) => optional($n->mapel)->kategori === 'kejuruan')
                ->avg('nilai');

            $kreatif = $siswa->nilaiMapel
                ->filter(fn($n) => optional($n->mapel)->kategori === 'kreatif')
                ->avg('nilai');

            $akademik = optional($siswa->nilaiAkademik)->rata_rata_nr;
            $absensiVal = optional($siswa->absensi)->rata_rata_persen;

            $dataMentah[$siswa->id] = [
                'C1' => (float) ($akademik ?? 0),
                'C2' => (float) ($umum ?? 0),
                'C3' => (float) ($kejuruan ?? 0),
                'C4' => (float) ($absensiVal ?? 0),
                'C5' => (float) ($kreatif ?? 0),
            ];
        }

        // 2) Cari nilai maksimum tiap kolom kriteria (semua kriteria benefit)
        $nilaiMax = [];
        $nilaiMin = [];
        foreach (['C1', 'C2', 'C3', 'C4', 'C5'] as $kode) {
            $kolom = array_column($dataMentah, $kode);
            $nilaiMax[$kode] = max($kolom);
            $positif = array_filter($kolom, fn($v) => $v > 0);
            $nilaiMin[$kode] = $positif ? min($positif) : 0;
        }


        // 3) Normalisasi SAW:
        //    benefit: rij = xij / max(xj)
        //    cost   : rij = min(xj) / xij
        $hasilPerhitungan = [];

        foreach ($dataMentah as $siswaId => $row) {
            $skor = 0;

            foreach ($row as $kode => $nilai) {
                if ($kriteria[$kode]->atribut === 'cost') {
                    $r = $nilai > 0 ? ($nilaiMin[$kode] / $nilai) : 0;
                } else {
                    $r = $nilaiMax[$kode] > 0 ? ($nilai / $nilaiMax[$kode]) : 0;
                }

                $skor += $r * ($kriteria[$kode]->bobot / 100);
            }

            $hasilPerhitungan[$siswaId] = [
                'nilai' => $row,
                'skor' => $skor,
            ];
        }


        // 4) Urutkan dari skor tertinggi -> terendah, lalu tetapkan ranking
        uasort($hasilPerhitungan, fn($a, $b) => $b['skor'] <=> $a['skor']);

        $ranking = 1;
        foreach ($hasilPerhitungan as $siswaId => $h) {
            HasilSpk::updateOrCreate(
                ['siswa_id' => $siswaId, 'semester' => $semester],
                [
                    'nilai_akademik' => $h['nilai']['C1'],
                    'nilai_umum' => $h['nilai']['C2'],
                    'nilai_kejuruan' => $h['nilai']['C3'],
                    'nilai_absensi' => $h['nilai']['C4'],
                    'nilai_kreatif' => $h['nilai']['C5'],
                    'skor_akhir' => $h['skor'],
                    'ranking' => $ranking,
                ]
            );
            $ranking++;
        }

        return redirect()->route('spk.hasil', ['semester' => $semester])
            ->with('success', 'Proses perhitungan SPK selesai.');
    }

    /**
     * Tampilkan hasil ranking siswa terbaik untuk semester tertentu.
     */
    public function hasil(Request $request)
    {
        $semester = $request->query('semester');

        $hasil = HasilSpk::with('siswa')
            ->when($semester, fn($q) => $q->where('semester', $semester))
            ->orderBy('ranking')
            ->get();

        return view('spk.hasil', compact('hasil', 'semester'));
    }

    /**
     * Cetak hasil ranking sebagai PDF.
     */
    public function cetakPdf(Request $request)
    {
        $semester = $request->query('semester');

        $hasil = HasilSpk::with('siswa')
            ->when($semester, fn($q) => $q->where('semester', $semester))
            ->orderBy('ranking')
            ->get();

        $pdf = Pdf::loadView('spk.pdf', compact('hasil', 'semester'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-spk-siswa-terbaik.pdf');
    }
}
