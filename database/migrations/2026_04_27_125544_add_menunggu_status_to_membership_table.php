<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE membership MODIFY COLUMN status ENUM('aktif', 'tidak aktif', 'nonaktif', 'menunggu') DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE membership MODIFY COLUMN status ENUM('aktif', 'tidak aktif') DEFAULT 'aktif'");
    }
};