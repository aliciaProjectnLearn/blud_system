<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_futsal', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_futsal', 'jenis_pembayaran')) {
                $table->enum('jenis_pembayaran', ['reguler', 'membership'])->default('reguler')->after('durasi_main');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_futsal', function (Blueprint $table) {
            if (Schema::hasColumn('booking_futsal', 'jenis_pembayaran')) {
                $table->dropColumn('jenis_pembayaran');
            }
        });
    }
};
