<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Karena mengubah ENUM di doctrine/dbal sering bermasalah jika ada modifikasi sebelumnya,
        // menggunakan statement raw SQL adalah solusi paling aman untuk MySQL.
        DB::statement("ALTER TABLE booking_servis MODIFY COLUMN status ENUM('menunggu', 'diproses', 'siap_bayar', 'selesai', 'batal') DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE booking_servis MODIFY COLUMN status ENUM('menunggu', 'diproses', 'selesai', 'batal') DEFAULT 'menunggu'");
    }
};
