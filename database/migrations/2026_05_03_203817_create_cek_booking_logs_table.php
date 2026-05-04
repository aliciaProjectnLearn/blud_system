<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cek_booking_logs', function (Blueprint $table) {
            $table->id();
            $table->string('no_hp', 20);
            $table->string('aksi');        // otp_dikirim, otp_salah, verifikasi_sukses, dll
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['no_hp', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cek_booking_logs');
    }
};
