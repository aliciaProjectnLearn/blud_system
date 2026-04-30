<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pembagian_pendapatan MODIFY COLUMN sistem ENUM('ac', 'kantin', 'futsal', 'servis') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pembagian_pendapatan MODIFY COLUMN sistem ENUM('ac', 'kantin', 'futsal') NOT NULL");
    }
};
