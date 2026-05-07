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
        Schema::table('penggajian_teknisi', function (Blueprint $table) {
            $table->enum('status_bayar', ['belum', 'sudah', 'dibayar'])->default('belum')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggajian_teknisi', function (Blueprint $table) {
            $table->enum('status_bayar', ['belum', 'sudah'])->default('belum')->change();
        });
    }
};
