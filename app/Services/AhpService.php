<?php

namespace App\Services;

use App\Models\AhpHasil;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AhpService
{
    // Random Index (Saaty)
    const RI = [
        1 => 0, 2 => 0, 3 => 0.58, 4 => 0.90, 5 => 1.12,
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
    ];

    const SKALA_OPSI = [
        '9'   => '9 - Mutlak lebih penting',
        '8'   => '8',
        '7'   => '7 - Sangat lebih penting',
        '6'   => '6',
        '5'   => '5 - Lebih penting',
        '4'   => '4',
        '3'   => '3 - Cukup lebih penting',
        '2'   => '2',
        '1'   => '1 - Sama penting',
        '1/2' => '1/2',
        '1/3' => '1/3 - Cukup kurang penting',
        '1/4' => '1/4',
        '1/5' => '1/5 - Kurang penting',
        '1/6' => '1/6',
        '1/7' => '1/7 - Sangat kurang penting',
        '1/8' => '1/8',
        '1/9' => '1/9 - Mutlak kurang penting',
    ];

    public function getKriterias()
    {
        return Kriteria::orderBy('kode')->get();
    }

    /** Nilai awal form: ambil dari perhitungan AHP terakhir, default 1. */
    public function nilaiForm($kriterias): array
    {
        $terakhir = AhpHasil::latest()->first();
        $input = $terakhir->detail['input'] ?? [];

        $form = [];
        foreach ($kriterias as $i => $a) {
            foreach ($kriterias as $j => $b) {
                if ($i < $j) {
                    $form[$a->id][$b->id] = $input[$a->id][$b->id] ?? '1';
                }
            }
        }

        return $form;
    }

    public function parseNilai(string $v): float
    {
        if (str_contains($v, '/')) {
            [$x, $y] = explode('/', $v);
            return (float) $x / (float) $y;
        }

        return (float) $v;
    }

    /**
     * $input = [idA][idB] => "3" atau "1/3"  (hanya segitiga atas)
     */
    public function jalankan(array $input): AhpHasil
    {
        $kriterias = $this->getKriterias()->values();
        $n = $kriterias->count();

        if ($n < 2) {
            throw new RuntimeException('Minimal 2 kriteria untuk AHP.');
        }

        // 1) Susun matriks perbandingan berpasangan
        $m = array_fill(0, $n, array_fill(0, $n, 1.0));
        foreach ($kriterias as $i => $a) {
            foreach ($kriterias as $j => $b) {
                if ($i < $j) {
                    $nilai = $this->parseNilai((string) ($input[$a->id][$b->id] ?? '1'));
                    if ($nilai <= 0) {
                        throw new RuntimeException('Nilai perbandingan tidak valid.');
                    }
                    $m[$i][$j] = $nilai;
                    $m[$j][$i] = 1 / $nilai;
                }
            }
        }

        // 2) Jumlah tiap kolom
        $jumlahKolom = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $jumlahKolom[$j] += $m[$i][$j];
            }
        }

        // 3) Normalisasi + bobot prioritas (rata-rata baris)
        $norm = [];
        $bobot = array_fill(0, $n, 0.0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $norm[$i][$j] = $m[$i][$j] / $jumlahKolom[$j];
                $bobot[$i] += $norm[$i][$j];
            }
            $bobot[$i] /= $n;
        }

        // 4) Lambda max, CI, CR
        $lambdaMax = 0.0;
        for ($j = 0; $j < $n; $j++) {
            $lambdaMax += $jumlahKolom[$j] * $bobot[$j];
        }
        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri = self::RI[$n] ?? 1.49;
        $cr = $ri > 0 ? $ci / $ri : 0;

        $bobotById = [];
        foreach ($kriterias as $i => $k) {
            $bobotById[$k->id] = $bobot[$i];
        }

        return AhpHasil::create([
            'lambda_max'   => $lambdaMax,
            'ci'           => $ci,
            'ri'           => $ri,
            'cr'           => $cr,
            'is_konsisten' => $cr <= 0.1,
            'detail'       => [
                'input'        => $input,
                'kriteria'     => $kriterias->map(fn ($k) => [
                    'id' => $k->id, 'kode' => $k->kode, 'nama' => $k->nama_kriteria,
                ])->all(),
                'matriks'      => $m,
                'jumlah_kolom' => $jumlahKolom,
                'normalisasi'  => $norm,
                'bobot'        => $bobotById,
            ],
        ]);
    }

    /** Salin bobot AHP (dalam persen) ke tabel kriteria. */
    public function terapkan(AhpHasil $hasil): void
    {
        if (! $hasil->is_konsisten) {
            throw new RuntimeException('Hasil AHP tidak konsisten (CR > 0,1). Bobot tidak dapat diterapkan.');
        }

        DB::transaction(function () use ($hasil) {
            $bobot = $hasil->detail['bobot'];
            $persen = [];
            foreach ($bobot as $id => $w) {
                $persen[$id] = round($w * 100, 2);
            }

            // koreksi selisih pembulatan supaya total tepat 100
            $selisih = round(100 - array_sum($persen), 2);
            $idTerbesar = array_search(max($persen), $persen);
            $persen[$idTerbesar] = round($persen[$idTerbesar] + $selisih, 2);

            foreach ($persen as $id => $p) {
                Kriteria::where('id', $id)->update(['bobot' => $p]);
            }

            AhpHasil::whereNotNull('diterapkan_at')->update(['diterapkan_at' => null]);
            $hasil->update(['diterapkan_at' => now()]);
        });
    }
}

