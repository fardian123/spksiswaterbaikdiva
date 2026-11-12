<?php
namespace App\Http\Controllers;

use App\Models\AhpHasil;
use App\Services\AhpService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class AhpController extends Controller
{
    public function __construct(private AhpService $ahp)
    {
    }

    public function index()
    {
        $kriterias = $this->ahp->getKriterias()->values();

        return view('ahp.index', [
            'kriterias'     => $kriterias,
            'nilaiForm'     => $this->ahp->nilaiForm($kriterias),
            'skalaOpsi'     => AhpService::SKALA_OPSI,
            'hasilTerakhir' => AhpHasil::latest()->first(),
        ]);
    }

    public function hitung(Request $request)
    {
        $request->validate([
            'perbandingan'     => 'required|array',
            'perbandingan.*.*' => ['required', Rule::in(array_keys(AhpService::SKALA_OPSI))],
        ]);

        try {
            $hasil = $this->ahp->jalankan($request->input('perbandingan'));
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $pesan = $hasil->is_konsisten
            ? 'Perhitungan AHP selesai. Hasil KONSISTEN, silakan terapkan bobot.'
            : 'Perhitungan AHP selesai, tetapi TIDAK konsisten (CR > 0,1). Revisi perbandingan.';

        return redirect()->route('ahp.hasil', $hasil)
            ->with($hasil->is_konsisten ? 'success' : 'error', $pesan);
    }

    public function hasil(AhpHasil $ahpHasil)
    {
        return view('ahp.hasil', ['hasil' => $ahpHasil, 'detail' => $ahpHasil->detail]);
    }

    public function terapkan(AhpHasil $ahpHasil)
    {
        try {
            $this->ahp->terapkan($ahpHasil);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('kriteria.index')
            ->with('success', 'Bobot AHP berhasil diterapkan ke kriteria.');
    }
}