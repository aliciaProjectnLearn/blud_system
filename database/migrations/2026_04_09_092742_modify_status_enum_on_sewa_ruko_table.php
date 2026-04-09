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
        DB::statement("ALTER TABLE sewa_ruko MODIFY COLUMN status ENUM('pending', 'aktif', 'selesai', 'dibatalkan', 'ditolak') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back and safely set any default just in case
        DB::statement("ALTER TABLE sewa_ruko MODIFY COLUMN status ENUM('aktif', 'selesai', 'dibatalkan') DEFAULT 'aktif'");
    }
};
