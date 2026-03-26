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
        Schema::table('booking_futsal', function (Blueprint $table) {
            $table->enum('jenis_pembayaran', ['reguler', 'membership'])->default('reguler')->after('durasi_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_futsal', function (Blueprint $table) {
            $table->dropColumn('jenis_pembayaran');
        });
    }
};
