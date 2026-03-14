<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_ac', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('booking_ac')->onDelete('restrict');
            $table->foreignId('layanan_ac_id')->constrained('layanan_ac')->onDelete('restrict');
            $table->dateTime('tgl_servis')->nullable();
            $table->integer('total_harga_jasa')->default(0);
            $table->integer('total_biaya')->default(0);
            $table->dateTime('tgl_bayar')->nullable();
            $table->foreignId('tipe_pembayaran_id')->constrained('tipe_pembayaran')->onDelete('restrict');
            $table->enum('status', ['menunggu', 'verifikasi'])->default('menunggu');
            $table->string('bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_ac');
    }
};
