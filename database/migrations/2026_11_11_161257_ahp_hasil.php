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