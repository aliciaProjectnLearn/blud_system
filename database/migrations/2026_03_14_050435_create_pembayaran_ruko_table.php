<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_ruko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('booking')->onDelete('restrict');
            $table->foreignId('tipe_pembayaran_id')->constrained('tipe_pembayaran')->onDelete('restrict');
            $table->enum('termin', [1, 2])->comment('1 = termin pertama, 2 = termin kedua');
            $table->dateTime('tgl_jatuh_tempo')->nullable();
            $table->integer('jumlah_tagihan');
            $table->dateTime('tgl_bayar')->nullable();
            $table->enum('status', ['menunggu', 'verifikasi', 'dibatalkan'])->default('menunggu');
            $table->string('no_kwitansi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_ruko');
    }
};
