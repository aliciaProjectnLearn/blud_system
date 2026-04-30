<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran_futsals', function(Blueprint $table) {
            if (!Schema::hasColumn('pengeluaran_futsals', 'kategori')) {
                $table->enum('kategori', ['pemeliharaan', 'gaji_penjaga', 'lainnya'])
                      ->default('lainnya')->after('deskripsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran_futsals', function(Blueprint $table) {
            if (Schema::hasColumn('pengeluaran_futsals', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
