Catatan: gambar 1 tidak terlampir di pesan ini, jadi saya tidak bisa melihatnya. Saya memakai gaya Bootstrap yang sama dengan view Kriteria Anda. Kalau tampilan harus persis seperti gambar, kirim ulang gambarnya dan saya sesuaikan. Kode AhpService di project lama juga tidak ikut terlampir, jadi saya buat versi baru yang lebih ringkas.

Cara kerjanya: admin mengisi perbandingan berpasangan 5 kriteria (C1-C5), sistem menghitung bobot AHP beserta CR, lalu bobot diterapkan ke tabel kriteria. SAW tetap dipakai untuk ranking dan dilengkapi dukungan cost.

RINGKASAN PERUBAHAN

TAMBAH
1. database/migrations/xxxx_create_ahp_hasil_table.php
2. app/Models/AhpHasil.php
3. app/Services/AhpService.php
4. app/Http/Controllers/AhpController.php
5. resources/views/ahp/index.blade.php
6. resources/views/ahp/hasil.blade.php

UBAH
7. routes/web.php
8. app/Http/Controllers/KriteriaController.php
9. resources/views/kriteria/index.blade.php
10. app/Http/Controllers/SpkController.php (bagian SAW)
11. resources/views/layouts/app.blade.php (menu)

HAPUS
Tidak ada file yang dihapus. Method updateBobot di KriteriaController diganti, lihat langkah 8.

========================================
1. MIGRATION
========================================
Jalankan: php artisan make:migration create_ahp_hasil_table

File: database/migrations/xxxx_create_ahp_hasil_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ahp_hasil', function (Blueprint $table) {
            $table->id();
            $table->decimal('lambda_max', 10, 4);
            $table->decimal('ci', 10, 4);
            $table->decimal('ri', 10, 2);
            $table->decimal('cr', 10, 4);
            $table->boolean('is_konsisten')->default(false);
            $table->json('detail');
            $table->timestamp('diterapkan_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ahp_hasil');
    }
};

Lalu: php artisan migrate

========================================
2. MODEL
========================================
File: app/Models/AhpHasil.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhpHasil extends Model
{
    protected $table = 'ahp_hasil';
    protected $guarded = [];

    protected $casts = [
        'detail'        => 'array',
        'is_konsisten'  => 'boolean',
        'diterapkan_at' => 'datetime',
    ];
}

========================================
3. SERVICE
========================================
File baru: app/Services/AhpService.php (buat folder Services jika belum ada)

<?php

namespace App\Services;

use App\Models\AhpHasil;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AhpService
{
    // Random Index (Saaty)
    const RI = [
        1 => 0, 2 => 0, 3 => 0.58, 4 => 0.90, 5 => 1.12,
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
    ];

    const SKALA_OPSI = [
        '9'   => '9 - Mutlak lebih penting',
        '8'   => '8',
        '7'   => '7 - Sangat lebih penting',
        '6'   => '6',
        '5'   => '5 - Lebih penting',
        '4'   => '4',
        '3'   => '3 - Cukup lebih penting',
        '2'   => '2',
        '1'   => '1 - Sama penting',
        '1/2' => '1/2',
        '1/3' => '1/3 - Cukup kurang penting',
        '1/4' => '1/4',
        '1/5' => '1/5 - Kurang penting',
        '1/6' => '1/6',
        '1/7' => '1/7 - Sangat kurang penting',
        '1/8' => '1/8',
        '1/9' => '1/9 - Mutlak kurang penting',
    ];

    public function getKriterias()
    {
        return Kriteria::orderBy('kode')->get();
    }

    /** Nilai awal form: ambil dari perhitungan AHP terakhir, default 1. */
    public function nilaiForm($kriterias): array
    {
        $terakhir = AhpHasil::latest()->first();
        $input = $terakhir->detail['input'] ?? [];

        $form = [];
        foreach ($kriterias as $i => $a) {
            foreach ($kriterias as $j => $b) {
                if ($i < $j) {
                    $form[$a->id][$b->id] = $input[$a->id][$b->id] ?? '1';
                }
            }
        }

        return $form;
    }

    public function parseNilai(string $v): float
    {
        if (str_contains($v, '/')) {
            [$x, $y] = explode('/', $v);
            return (float) $x / (float) $y;
        }

        return (float) $v;
    }

    /**
     * $input = [idA][idB] => "3" atau "1/3"  (hanya segitiga atas)
     */
    public function jalankan(array $input): AhpHasil
    {
        $kriterias = $this->getKriterias()->values();
        $n = $kriterias->count();

        if ($n < 2) {
            throw new RuntimeException('Minimal 2 kriteria untuk AHP.');
        }

        // 1) Susun matriks perbandingan berpasangan
        $m = array_fill(0, $n, array_fill(0, $n, 1.0));
        foreach ($kriterias as $i => $a) {
            foreach ($kriterias as $j => $b) {
                if ($i < $j) {
                    $nilai = $this->parseNilai((string) ($input[$a->id][$b->id] ?? '1'));
                    if ($nilai <= 0) {
                        throw new RuntimeException('Nilai perbandingan tidak valid.');
                    }
                    $m[$i][$j] = $nilai;
                    $m[$j][$i] = 1 / $nilai;
                }
            }
        }

        // 2) Jumlah tiap kolom
        $jumlahKolom = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $jumlahKolom[$j] += $m[$i][$j];
            }
        }

        // 3) Normalisasi + bobot prioritas (rata-rata baris)
        $norm = [];
        $bobot = array_fill(0, $n, 0.0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $norm[$i][$j] = $m[$i][$j] / $jumlahKolom[$j];
                $bobot[$i] += $norm[$i][$j];
            }
            $bobot[$i] /= $n;
        }

        // 4) Lambda max, CI, CR
        $lambdaMax = 0.0;
        for ($j = 0; $j < $n; $j++) {
            $lambdaMax += $jumlahKolom[$j] * $bobot[$j];
        }
        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri = self::RI[$n] ?? 1.49;
        $cr = $ri > 0 ? $ci / $ri : 0;

        $bobotById = [];
        foreach ($kriterias as $i => $k) {
            $bobotById[$k->id] = $bobot[$i];
        }

        return AhpHasil::create([
            'lambda_max'   => $lambdaMax,
            'ci'           => $ci,
            'ri'           => $ri,
            'cr'           => $cr,
            'is_konsisten' => $cr <= 0.1,
            'detail'       => [
                'input'        => $input,
                'kriteria'     => $kriterias->map(fn ($k) => [
                    'id' => $k->id, 'kode' => $k->kode, 'nama' => $k->nama_kriteria,
                ])->all(),
                'matriks'      => $m,
                'jumlah_kolom' => $jumlahKolom,
                'normalisasi'  => $norm,
                'bobot'        => $bobotById,
            ],
        ]);
    }

    /** Salin bobot AHP (dalam persen) ke tabel kriteria. */
    public function terapkan(AhpHasil $hasil): void
    {
        if (! $hasil->is_konsisten) {
            throw new RuntimeException('Hasil AHP tidak konsisten (CR > 0,1). Bobot tidak dapat diterapkan.');
        }

        DB::transaction(function () use ($hasil) {
            $bobot = $hasil->detail['bobot'];
            $persen = [];
            foreach ($bobot as $id => $w) {
                $persen[$id] = round($w * 100, 2);
            }

            // koreksi selisih pembulatan supaya total tepat 100
            $selisih = round(100 - array_sum($persen), 2);
            $idTerbesar = array_search(max($persen), $persen);
            $persen[$idTerbesar] = round($persen[$idTerbesar] + $selisih, 2);

            foreach ($persen as $id => $p) {
                Kriteria::where('id', $id)->update(['bobot' => $p]);
            }

            AhpHasil::whereNotNull('diterapkan_at')->update(['diterapkan_at' => null]);
            $hasil->update(['diterapkan_at' => now()]);
        });
    }
}

========================================
4. CONTROLLER
========================================
File: app/Http/Controllers/AhpController.php

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

========================================
5. ROUTES
========================================
File: routes/web.php
Tambahkan use di atas:

use App\Http\Controllers\AhpController;

Tambahkan di dalam group middleware yang sama dengan route kriteria Anda:

Route::get('ahp', [AhpController::class, 'index'])->name('ahp.index');
Route::post('ahp/hitung', [AhpController::class, 'hitung'])->name('ahp.hitung');
Route::get('ahp/hasil/{ahpHasil}', [AhpController::class, 'hasil'])->name('ahp.hasil');
Route::post('ahp/{ahpHasil}/terapkan', [AhpController::class, 'terapkan'])->name('ahp.terapkan');

Ganti route lama updateBobot menjadi:

Route::post('kriteria/atribut', [KriteriaController::class, 'updateAtribut'])->name('kriteria.updateAtribut');

(hapus baris Route::...->name('kriteria.updateBobot'))

========================================
6. VIEW AHP INDEX
========================================
File: resources/views/ahp/index.blade.php

@extends('layouts.app')

@section('title', 'AHP - Bobot Kriteria')

@section('content')
<h4 class="mb-3">Perbandingan Kriteria (AHP)</h4>

<div class="card border-0 shadow-sm">
    <form action="{{ route('ahp.hitung') }}" method="POST">
        @csrf
        <div class="card-body">
            <p class="text-muted small">
                Pilih seberapa penting kriteria kiri dibanding kriteria kanan
                (skala Saaty 1-9). Nilai pecahan berarti kriteria kiri LEBIH TIDAK penting.
            </p>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kriteria A</th>
                            <th width="320">Perbandingan</th>
                            <th>Kriteria B</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriterias as $i => $a)
                            @foreach ($kriterias as $j => $b)
                                @if ($i < $j)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $a->kode }}</span> {{ $a->nama_kriteria }}</td>
                                        <td>
                                            <select name="perbandingan[{{ $a->id }}][{{ $b->id }}]" class="form-select form-select-sm">
                                                @foreach ($skalaOpsi as $val => $label)
                                                    <option value="{{ $val }}"
                                                        @selected((string) old('perbandingan.' . $a->id . '.' . $b->id, $nilaiForm[$a->id][$b->id]) === (string) $val)>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $b->kode }}</span> {{ $b->nama_kriteria }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <button class="btn btn-primary"><i class="bi bi-calculator me-1"></i>Hitung AHP</button>
            @if ($hasilTerakhir)
                <a href="{{ route('ahp.hasil', $hasilTerakhir) }}" class="btn btn-outline-secondary ms-2">Lihat Hasil Terakhir</a>
            @endif
        </div>
    </form>
</div>
@endsection

========================================
6b. VIEW AHP HASIL
========================================
File: resources/views/ahp/hasil.blade.php

@extends('layouts.app')

@section('title', 'Hasil AHP')

@section('content')
@php
    $kr = $detail['kriteria'];
    $n  = count($kr);
@endphp

<h4 class="mb-3">Hasil Perhitungan AHP</h4>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold">Matriks Perbandingan Berpasangan</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th></th>
                    @foreach ($kr as $k) <th>{{ $k['kode'] }}</th> @endforeach
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $n; $i++)
                    <tr>
                        <th class="table-light">{{ $kr[$i]['kode'] }}</th>
                        @for ($j = 0; $j < $n; $j++)
                            <td>{{ number_format($detail['matriks'][$i][$j], 4) }}</td>
                        @endfor
                    </tr>
                @endfor
                <tr class="table-light fw-bold">
                    <td>Jumlah</td>
                    @for ($j = 0; $j < $n; $j++)
                        <td>{{ number_format($detail['jumlah_kolom'][$j], 4) }}</td>
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold">Normalisasi & Bobot Prioritas</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th></th>
                    @foreach ($kr as $k) <th>{{ $k['kode'] }}</th> @endforeach
                    <th>Bobot</th>
                    <th>Bobot (%)</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $n; $i++)
                    <tr>
                        <th class="table-light">{{ $kr[$i]['kode'] }} - {{ $kr[$i]['nama'] }}</th>
                        @for ($j = 0; $j < $n; $j++)
                            <td>{{ number_format($detail['normalisasi'][$i][$j], 4) }}</td>
                        @endfor
                        <td>{{ number_format($detail['bobot'][$kr[$i]['id']], 4) }}</td>
                        <td class="fw-bold">{{ number_format($detail['bobot'][$kr[$i]['id']] * 100, 2) }}%</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold">Uji Konsistensi</div>
    <div class="card-body">
        <table class="table table-sm w-auto mb-3">
            <tr><td>Lambda max</td><td>{{ number_format($hasil->lambda_max, 4) }}</td></tr>
            <tr><td>CI</td><td>{{ number_format($hasil->ci, 4) }}</td></tr>
            <tr><td>RI (n = {{ $n }})</td><td>{{ number_format($hasil->ri, 2) }}</td></tr>
            <tr><td>CR</td><td>{{ number_format($hasil->cr, 4) }}</td></tr>
            <tr>
                <td>Status</td>
                <td>
                    @if ($hasil->is_konsisten)
                        <span class="badge bg-success">Konsisten (CR &le; 0,1)</span>
                    @else
                        <span class="badge bg-danger">Tidak konsisten (CR &gt; 0,1)</span>
                    @endif
                </td>
            </tr>
        </table>

        <a href="{{ route('ahp.index') }}" class="btn btn-outline-secondary">Ubah Perbandingan</a>

        @if ($hasil->is_konsisten)
            <form action="{{ route('ahp.terapkan', $hasil) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Terapkan bobot ini ke kriteria?')">
                @csrf
                <button class="btn btn-success">Terapkan Bobot ke Kriteria</button>
            </form>
        @endif

        @if ($hasil->diterapkan_at)
            <span class="text-success small ms-2">Diterapkan {{ $hasil->diterapkan_at->format('d/m/Y H:i') }}</span>
        @endif
    </div>
</div>
@endsection

========================================
7. KRITERIA CONTROLLER
========================================
File: app/Http/Controllers/KriteriaController.php
Ganti seluruh method updateBobot dengan:

    /**
     * Bobot sekarang ditentukan oleh AHP. Di sini hanya atribut
     * (benefit/cost) yang bisa diubah.
     */
    public function updateAtribut(Request $request)
    {
        $validated = $request->validate([
            'atribut'   => 'required|array',
            'atribut.*' => 'required|in:benefit,cost',
        ]);

        foreach ($validated['atribut'] as $kriteriaId => $atribut) {
            Kriteria::where('id', $kriteriaId)->update(['atribut' => $atribut]);
        }

        return redirect()->route('kriteria.index')
            ->with('success', 'Atribut kriteria berhasil diperbarui.');
    }

Hapus juga "use Illuminate\Validation\ValidationException;" karena sudah tidak dipakai.

========================================
8. VIEW KRITERIA (ubah)
========================================
File: resources/views/kriteria/index.blade.php

a) Ganti baris pembuka form:
   <form action="{{ route('kriteria.updateBobot') }}" method="POST">
   menjadi:
   <form action="{{ route('kriteria.updateAtribut') }}" method="POST">

b) Ganti <td> input bobot:
                            <td>
                                <input type="number" step="0.01" min="0" max="100"
                                       name="bobot[{{ $k->id }}]" class="form-control form-control-sm input-bobot"
                                       value="{{ old('bobot.' . $k->id, $k->bobot) }}" required>
                            </td>
   menjadi:
                            <td>
                                <span class="fw-bold">{{ number_format($k->bobot, 2) }}</span>
                            </td>

c) Ganti <th><span id="total-bobot" class="fw-bold">0</span>%</th> menjadi:
   <th>{{ number_format($kriteria->sum('bobot'), 2) }}%</th>

d) Ganti isi card-footer menjadi:
        <div class="card-footer bg-white">
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Atribut</button>
            <a href="{{ route('ahp.index') }}" class="btn btn-outline-success ms-2">
                <i class="bi bi-diagram-3 me-1"></i>Tentukan Bobot dengan AHP
            </a>
        </div>

e) Hapus seluruh blok @push('scripts') ... @endpush (JS hitungTotal sudah tidak diperlukan).

========================================
9. SPK CONTROLLER (SAW, dukung cost)
========================================
File: app/Http/Controllers/SpkController.php
Method proses(): ganti bagian "// 2) Cari nilai maksimum ..." sampai akhir "// 3) Normalisasi ..." (sebelum "// 4) Urutkan") dengan:

        // 2) Cari nilai max (untuk benefit) dan min (untuk cost) tiap kriteria
        $nilaiMax = [];
        $nilaiMin = [];
        foreach (['C1', 'C2', 'C3', 'C4', 'C5'] as $kode) {
            $kolom = array_column($dataMentah, $kode);
            $nilaiMax[$kode] = max($kolom);
            $positif = array_filter($kolom, fn ($v) => $v > 0);
            $nilaiMin[$kode] = $positif ? min($positif) : 0;
        }

        // 3) Normalisasi SAW:
        //    benefit: rij = xij / max(xj)
        //    cost   : rij = min(xj) / xij
        $hasilPerhitungan = [];

        foreach ($dataMentah as $siswaId => $row) {
            $skor = 0;

            foreach ($row as $kode => $nilai) {
                if ($kriteria[$kode]->atribut === 'cost') {
                    $r = $nilai > 0 ? ($nilaiMin[$kode] / $nilai) : 0;
                } else {
                    $r = $nilaiMax[$kode] > 0 ? ($nilai / $nilaiMax[$kode]) : 0;
                }

                $skor += $r * ($kriteria[$kode]->bobot / 100);
            }

            $hasilPerhitungan[$siswaId] = [
                'nilai' => $row,
                'skor'  => $skor,
            ];
        }

Di awal method yang sama, ganti pengecekan bobot:
   if ($kriteria->sum('bobot') != 100) {
menjadi:
   if (round($kriteria->sum('bobot'), 2) != 100) {

Ganti juga pesan errornya menjadi:
   'Total bobot kriteria belum 100%. Hitung dulu bobot lewat menu AHP lalu terapkan.'

Method formProses() juga memakai $totalBobot = $kriteria->sum('bobot'); tidak perlu diubah.

Update docblock method proses(): ubah kalimat "Semua 5 kriteria bersifat benefit" menjadi "Atribut benefit/cost mengikuti tabel kriteria".

========================================
10. MENU SIDEBAR
========================================
File: resources/views/layouts/app.blade.php
Cari link menu Kriteria, lalu tambahkan di bawahnya (sesuaikan class dengan menu lain):

<a class="nav-link {{ request()->routeIs('ahp.*') ? 'active' : '' }}" href="{{ route('ahp.index') }}">
    <i class="bi bi-diagram-3 me-2"></i>AHP
</a>

========================================
ALUR PEMAKAIAN
========================================
1. php artisan migrate
2. Menu AHP, isi perbandingan, klik Hitung AHP.
3. Pastikan CR <= 0,1, lalu klik Terapkan Bobot ke Kriteria.
4. Menu Proses SPK, pilih semester. SAW memakai bobot dari AHP.

Catatan:
- Kalau CR > 0,1, ubah nilai perbandingan yang bertentangan (misalnya A > B, B > C, tetapi C > A) lalu hitung ulang.
- Setelah migrate, nilai kolom bobot lama di tabel kriteria tetap dipakai sampai Anda menerapkan hasil AHP pertama.
- Kalau mau riwayat perhitungan seperti di project lama, tambahkan method riwayat() yang memakai AhpHasil::latest()->paginate(15) dan satu view tabel. Datanya sudah tersimpan di tabel ahp_hasil.