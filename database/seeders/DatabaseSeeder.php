<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kriteria;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::updateOrCreate(['email' => 'admin@spk.test'], [
            'name' => 'Admin',
            'password' => Hash::make('admin123'),
        ]);

        $data = [
            ['C1', 'Akademik', 25],
            ['C2', 'Nilai Pelajaran Umum', 20],
            ['C3', 'Nilai Pelajaran Kejuruan', 25],
            ['C4', 'Absensi', 15],
            ['C5', 'Kreatif', 15],
        ];
        foreach ($data as [$kode, $nama, $bobot]) {
            Kriteria::updateOrCreate(['kode' => $kode], [
                'nama_kriteria' => $nama,
                'atribut' => 'benefit',
                'bobot' => $bobot,
            ]);
        }
    }
}
