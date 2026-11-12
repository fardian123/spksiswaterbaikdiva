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
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('kode');         // C1..C5
            $table->string('nama_kriteria'); // Akademik, Nilai Umum, Nilai Kejuruan, Absensi, Kreatif
            $table->enum('atribut', ['benefit', 'cost'])->default('benefit');
            $table->decimal('bobot', 5, 2); // total harus 100 atau 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
