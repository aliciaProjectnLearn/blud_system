<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_operasional_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapangan_id')->constrained('lapangan')->onDelete('cascade');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->unique(['lapangan_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_operasional_lapangan');
    }
};
