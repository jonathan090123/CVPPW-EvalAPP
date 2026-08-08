<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel ini menyimpan proporsi penilaian per level jabatan.
     * Mis: KEPALA BAGIAN butuh Inisiatif tinggi (proporsi 1.5x),
     *      STAFF tidak dituntut tinggi (0.8x). Di-set oleh Owner.
     */
    public function up(): void
    {
        Schema::create('level_proportions', function (Blueprint $table) {
            $table->id();
            $table->string('position', 100);
            $table->foreignId('criterion_id')->constrained()->cascadeOnDelete();
            $table->decimal('proportion', 5, 2)->default(1.00); // 1.00 = normal, 1.50 = 1.5x, 0.80 = 0.8x
            $table->timestamps();

            $table->unique(['position', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_proportions');
    }
};