<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan nilai 'event' ke ENUM jenis_pembayaran pada tabel booking_futsal.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE booking_futsal 
            MODIFY COLUMN jenis_pembayaran 
            ENUM('reguler', 'paket', 'event') 
            NOT NULL DEFAULT 'reguler'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE booking_futsal 
            MODIFY COLUMN jenis_pembayaran 
            ENUM('reguler', 'paket') 
            NOT NULL DEFAULT 'reguler'");
    }
};
