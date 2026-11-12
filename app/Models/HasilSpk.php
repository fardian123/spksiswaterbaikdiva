<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class HasilSpk extends Model
{
    use HasFactory;

    protected $table = 'hasil_spk';

    protected $fillable = [
        'siswa_id',
        'nilai_akademik',
        'nilai_umum',
        'nilai_kejuruan',
        'nilai_absensi',
        'nilai_kreatif',
        'skor_akhir',
        'ranking',
        'semester',
    ];
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }


}


