<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggajian_teknisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teknisi_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_servis_id')->constrained('booking_servis')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->enum('status_bayar', ['belum', 'sudah'])->default('belum');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggajian_teknisi');
    }
};
