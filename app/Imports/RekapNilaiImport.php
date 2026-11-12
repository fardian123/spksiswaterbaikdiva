<?php

namespace App\Imports;

use App\Models\NilaiMapel;
use App\Models\Siswa;
use App\Models\Mapel;
use Maatwebsite\Excel\Concerns\ToModel;

class RekapNilaiImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // ambil header asli (nama kolom mapel) dari row pertama file
        $headers = array_keys($rows->first()->toArray());

        // kolom fix: no, nis, nama_siswa, ..., jumlah, rata - rata
        $kolomMapel = array_diff($headers, ['no','nis','nama_siswa','jumlah','rata_rata']);

        // pastikan setiap kolom mapel terdaftar di tabel mapel (kalau belum ada, buat baru, kategori=null)
        foreach ($kolomMapel as $namaKolom) {
            Mapel::firstOrCreate(['nama_mapel' => $namaKolom]);
        }

        foreach ($rows as $row) {
            $siswa = Siswa::firstOrCreate(
                ['nis' => $row['nis']],
                ['nama' => $row['nama_siswa']]
            );

            foreach ($kolomMapel as $namaKolom) {
                if ($row[$namaKolom] === null || $row[$namaKolom] === '') continue;
                $mapel = Mapel::where('nama_mapel',$namaKolom)->first();

                NilaiMapel::updateOrCreate(
                    ['siswa_id'=>$siswa->id,'mapel_id'=>$mapel->id,'semester'=>request('semester','genap-2025/2026')],
                    ['nilai'=>$row[$namaKolom]]
                );
            }
        }
    }
}