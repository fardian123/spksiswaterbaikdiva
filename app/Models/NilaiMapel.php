<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiMapel extends Model
{
    use HasFactory;

    protected $table = 'nilai_mapel';

   protected $fillable = ['siswa_id', 'mapel_id', 'nilai', 'semester'];

    // Nilai mata pelajaran dimiliki oleh satu siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Nilai mata pelajaran dimiliki oleh satu mata pelajaran
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}
