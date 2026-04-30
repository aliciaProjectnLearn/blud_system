<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_servis_id')->constrained('booking_servis')->cascadeOnDelete();
            $table->string('kode_pembayaran')->unique();
            $table->decimal('total_biaya', 15, 2);
            $table->enum('tipe_pembayaran', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->enum('status_pembayaran', ['belum_bayar', 'dp', 'lunas'])->default('belum_bayar');
            $table->decimal('jumlah_dp', 15, 2)->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->string('path_bukti_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_servis');
    }
};
