<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewa_ruko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('booking')->onDelete('restrict');
            $table->foreignId('penyewa_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('ruko_id')->constrained('ruko')->onDelete('restrict');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('total_biaya_tahunan');
            $table->string('no_mou')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewa_ruko');
    }
};
