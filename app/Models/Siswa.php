<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
     use HasFactory;

    protected $table = 'siswa';

    protected $fillable = ['nis', 'nama', 'kelas', 'jurusan', 'tahun_ajaran'];

    // Siswa memiliki banyak nilai mata pelajaran
    public function nilaiMapel()
    {
        return $this->hasMany(NilaiMapel::class, 'siswa_id');
    }

    // Siswa memiliki satu data absensi
    public function absensi()
    {
        return $this->hasOne(Absensi::class, 'siswa_id');
    }

    // Siswa memiliki satu nilai akademik
    public function nilaiAkademik()
    {
        return $this->hasOne(NilaiAkademik::class, 'siswa_id');
    }

    // Siswa memiliki satu hasil SPK
    public function hasilSpk()
    {
        return $this->hasOne(HasilSpk::class, 'siswa_id');
    }
}