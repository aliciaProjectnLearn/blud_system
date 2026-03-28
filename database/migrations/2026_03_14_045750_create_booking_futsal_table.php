<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_futsal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('booking')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('lapangan_id')->constrained('lapangan')->onDelete('restrict');
            $table->date('tgl_main');
            $table->time('jam_mulai');
            $table->time('jam_mulai_efektif')->nullable();
            $table->time('jam_selesai');
            $table->integer('durasi_main')->default(1)->comment('dalam jam');
            $table->enum('jenis_pembayaran', ['reguler', 'membership'])->default('reguler');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_futsal');
    }
};
