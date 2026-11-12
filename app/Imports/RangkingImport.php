<?php

namespace App\Imports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\ToModel;

class RangkingImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $siswa = Siswa::firstOrCreate(
                ['nis' => $row['nis']],
                ['nama' => $row['nasis']]
            );

            Absensi::updateOrCreate(
                ['siswa_id' => $siswa->id, 'semester' => request('semester')],
                ['rata_rata_persen' => $row['rata_rata_abs']]
            );

            NilaiAkademik::updateOrCreate(
                ['siswa_id' => $siswa->id, 'semester' => request('semester')],
                ['rata_rata_nr' => $row['rata_rata_nr']]
            );
        }
    }
}