<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_ac', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('booking')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('layanan_id')->constrained('layanan_ac')->onDelete('restrict');
            $table->date('tgl_kunjungan');
            $table->string('alamat');
            $table->string('merek_ac')->nullable();
            $table->string('detail_keluhan')->nullable();
            $table->enum('status', ['menunggu', 'proses', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_ac');
    }
};
