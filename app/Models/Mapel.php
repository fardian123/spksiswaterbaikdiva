<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapel';

    protected $fillable = ['kode_mapel', 'nama_mapel', 'kategori'];

    // Satu mata pelajaran memiliki banyak nilai siswa
    public function nilaiMapel()
    {
        return $this->hasMany(NilaiMapel::class, 'mapel_id');
    }
}
