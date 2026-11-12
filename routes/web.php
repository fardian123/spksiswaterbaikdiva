<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AhpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\NilaiAkademikController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SpkController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]); // role hanya admin, register dimatikan

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Master data siswa
    Route::resource('siswa', SiswaController::class)->except(['show']);

    // Master data mapel + penentuan kategori
    Route::resource('mapel', MapelController::class)->except(['show']);
    Route::post('mapel/set-kategori', [MapelController::class, 'setKategori'])->name('mapel.setKategori');

    // Nilai mapel - manual & import
    Route::get('nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('nilai/{siswa}/edit', [NilaiController::class, 'edit'])->name('nilai.edit');
    Route::put('nilai/{siswa}', [NilaiController::class, 'update'])->name('nilai.update');
    Route::post('nilai/import', [NilaiController::class, 'import'])->name('nilai.import');

    // Absensi - manual & import
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('absensi/manual', [AbsensiController::class, 'storeManual'])->name('absensi.storeManual');
    Route::post('absensi/import', [AbsensiController::class, 'import'])->name('absensi.import');

    // Nilai akademik - manual
    Route::get('nilai-akademik', [NilaiAkademikController::class, 'index'])->name('nilai-akademik.index');
    Route::post('nilai-akademik/manual', [NilaiAkademikController::class, 'storeManual'])->name('nilai-akademik.storeManual');

    // Kriteria (bobot dari AHP, atribut bisa diubah manual)
    Route::get('kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
    Route::post('kriteria/atribut', [KriteriaController::class, 'updateAtribut'])->name('kriteria.updateAtribut');

    // AHP
    Route::get('ahp', [AhpController::class, 'index'])->name('ahp.index');
    Route::post('ahp/hitung', [AhpController::class, 'hitung'])->name('ahp.hitung');
    Route::get('ahp/hasil/{ahpHasil}', [AhpController::class, 'hasil'])->name('ahp.hasil');
    Route::post('ahp/{ahpHasil}/terapkan', [AhpController::class, 'terapkan'])->name('ahp.terapkan');

    // Proses SPK (SAW) & laporan
    Route::get('spk/proses', [SpkController::class, 'formProses'])->name('spk.formProses');
    Route::post('spk/proses', [SpkController::class, 'proses'])->name('spk.proses');
    Route::get('spk/hasil', [SpkController::class, 'hasil'])->name('spk.hasil');
    Route::get('spk/cetak-pdf', [SpkController::class, 'cetakPdf'])->name('spk.cetakPdf');
});
