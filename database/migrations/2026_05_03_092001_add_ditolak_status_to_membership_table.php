<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan nilai 'ditolak' ke ENUM status membership
        DB::statement("ALTER TABLE membership MODIFY COLUMN status ENUM('aktif', 'tidak aktif', 'nonaktif', 'menunggu', 'ditolak') DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        // Rollback: hapus nilai 'ditolak' (hati-hati jika ada data dengan status ditolak)
        DB::statement("ALTER TABLE membership MODIFY COLUMN status ENUM('aktif', 'tidak aktif', 'nonaktif', 'menunggu') DEFAULT 'menunggu'");
    }
};
