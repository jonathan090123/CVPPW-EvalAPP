<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Perbesar kolom dulu (decimal(5,4) -> decimal(6,2)) agar muat nilai persen
        Schema::table('criteria', function (Blueprint $table) {
            $table->decimal('weight', 6, 2)->default(0)->change();
        });

        // Konversi bobot dari format desimal (0..1) ke persen (0..100)
        DB::table('criteria')->update(['weight' => DB::raw('weight * 100')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('criteria')->update(['weight' => DB::raw('weight / 100')]);

        Schema::table('criteria', function (Blueprint $table) {
            $table->decimal('weight', 5, 4)->change();
        });
    }
};