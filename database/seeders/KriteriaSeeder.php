<?php

namespace Database\Seeders;
use App\Models\Kriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kriteria::insert([
            ['kode' => 'C1', 'nama_kriteria' => 'Akademik', 'atribut' => 'benefit', 'bobot' => 25],
            ['kode' => 'C2', 'nama_kriteria' => 'Nilai Pelajaran Umum', 'atribut' => 'benefit', 'bobot' => 20],
            ['kode' => 'C3', 'nama_kriteria' => 'Nilai Pelajaran Kejuruan', 'atribut' => 'benefit', 'bobot' => 25],
            ['kode' => 'C4', 'nama_kriteria' => 'Absensi', 'atribut' => 'benefit', 'bobot' => 15],
            ['kode' => 'C5', 'nama_kriteria' => 'Kreatif', 'atribut' => 'benefit', 'bobot' => 15],
        ]);
    }
}
