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
        Schema::create('mapel', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mapel')->nullable();
            $table->string('nama_mapel');            // hasil extract dari header excel
            $table->enum('kategori', ['umum', 'kejuruan', 'kreatif'])->nullable();
            // nullable karena saat import awal, kategori belum ditentukan admin
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
