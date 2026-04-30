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
        Schema::table('booking_servis', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'batal'])->default('menunggu')->change();
        });
    }

    public function down(): void
    {
        Schema::table('booking_servis', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu')->change();
        });
    }
};
