<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_servis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('layanan_servis_id')->nullable()->constrained('layanan_servis')->nullOnDelete();
            $table->enum('tipe_kendaraan', ['motor', 'mobil']);
            $table->string('merek_kendaraan');
            $table->string('nomor_plat');
            $table->year('tahun_kendaraan')->nullable();
            $table->text('keluhan');
            $table->date('tanggal_booking');
            $table->time('jam_booking');
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_servis');
    }
};
