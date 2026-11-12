<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\HasilSpk;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan data di halaman utama admin.
     */
    public function index()
    {
        $totalSiswa   = Siswa::count();
        $totalMapel   = Mapel::count();
        $belumDikategori = Mapel::whereNull('kategori')->count();

        $totalUmum     = Mapel::where('kategori', 'umum')->count();
        $totalKejuruan = Mapel::where('kategori', 'kejuruan')->count();
        $totalKreatif  = Mapel::where('kategori', 'kreatif')->count();

        // Top 5 ranking terakhir (kalau proses SPK sudah pernah dijalankan)
        $top5 = HasilSpk::with('siswa')
            ->orderBy('ranking')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSiswa',
            'totalMapel',
            'belumDikategori',
            'totalUmum',
            'totalKejuruan',
            'totalKreatif',
            'top5'
        ));
    }
}
