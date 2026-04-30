<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_ac', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->string('nama');
            $table->string('kapasitas_ac')->nullable();
            $table->integer('harga_jasa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_ac');
    }
};
