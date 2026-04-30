<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimoni', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('peran')->comment('Contoh: Pengguna Futsal, Penyewa Kantin');
            $table->tinyInteger('bintang')->default(5)->comment('1-5');
            $table->text('isi');
            $table->boolean('tampil')->default(true)->comment('true = ditampilkan di landing page');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimoni');
    }
};
