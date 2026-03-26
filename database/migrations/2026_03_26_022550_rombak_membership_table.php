<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel lama
        Schema::dropIfExists('membership');

        // Buat ulang dengan struktur baru
        Schema::create('membership', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('paket_membership_id')->constrained('paket_membership')->onDelete('cascade');
            $table->foreignId('transaksi_id')->nullable()->constrained('pembayaran_futsal')->onDelete('set null');
            $table->integer('total_kuota');
            $table->integer('sisa_kuota');
            $table->date('tgl_daftar');
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership');
    }
};
