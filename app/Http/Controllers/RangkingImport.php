<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\NilaiAkademik;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import untuk file seperti Rangking_XI_TE_2_1b_2526_xls.xls
 * Kolom: no, nis, nasis (nama siswa), rata_rata_abs, rata_rata_nr, ket
 *
 * Satu file ini mengisi DUA tabel sekaligus:
 * - absensi.rata_rata_persen   (dari kolom Rata-rata ABS)
 * - nilai_akademik.rata_rata_nr (dari kolom Rata-rata NR)
 */
class RangkingImport implements ToCollection, WithHeadingRow
{
    protected string $semester;

    public function __construct(string $semester)
    {
        $this->semester = $semester;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nis  = trim((string) ($row['nis'] ?? ''));
            $nama = trim((string) ($row['nasis'] ?? $row['nama'] ?? ''));

            if ($nis === '' || $nama === '') {
                continue;
            }

            $siswa = Siswa::firstOrCreate(
                ['nis' => $nis],
                ['nama' => $nama]
            );

            $rataAbs = $row['rata_rata_abs'] ?? null;
            $rataNr  = $row['rata_rata_nr'] ?? null;

            if ($rataAbs !== null && $rataAbs !== '') {
                Absensi::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'semester' => $this->semester],
                    ['rata_rata_persen' => (float) $rataAbs]
                );
            }

            if ($rataNr !== null && $rataNr !== '') {
                NilaiAkademik::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'semester' => $this->semester],
                    ['rata_rata_nr' => (float) $rataNr]
                );
            }
        }
    }
}
