<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_futsal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('booking')->onDelete('restrict');
            $table->foreignId('tipe_pembayaran_id')->constrained('tipe_pembayaran')->onDelete('restrict');
            $table->integer('jumlah_bayar');
            $table->enum('status', ['menunggu', 'verifikasi', 'dibatalkan'])->default('menunggu');
            $table->string('bukti')->nullable();
            $table->dateTime('tgl_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_futsal');
    }
};
