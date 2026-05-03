<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('token_sewa', 64);
            $table->string('no_hp', 15);
            $table->string('kode_otp', 6);
            $table->timestamp('expired_at');
            $table->boolean('sudah_digunakan')->default(false);
            $table->timestamps();

            $table->index('token_sewa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
