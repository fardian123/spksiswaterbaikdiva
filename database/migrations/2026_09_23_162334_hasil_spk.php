<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_spk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->decimal('nilai_akademik', 8, 3)->nullable();
            $table->decimal('nilai_umum', 8, 3)->nullable();
            $table->decimal('nilai_kejuruan', 8, 3)->nullable();
            $table->decimal('nilai_absensi', 8, 3)->nullable();
            $table->decimal('nilai_kreatif', 8, 3)->nullable();
            $table->decimal('skor_akhir', 10, 5)->nullable();
            $table->integer('ranking')->nullable();
            $table->string('semester')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_spk');
    }
};
