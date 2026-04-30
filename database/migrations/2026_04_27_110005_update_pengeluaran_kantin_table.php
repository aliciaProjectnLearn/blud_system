<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran_kantin', function (Blueprint $table) {
            if (!Schema::hasColumn('pengeluaran_kantin', 'kategori_pengeluaran')) {
                $table->enum('kategori_pengeluaran', ['pemeliharaan', 'operasional', 'lainnya'])->default('pemeliharaan');
            } else {
                $table->enum('kategori_pengeluaran', ['pemeliharaan', 'operasional', 'lainnya'])->default('pemeliharaan')->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran_kantin', function (Blueprint $table) {
            $table->enum('kategori_pengeluaran', ['pemeliharaan'])->default('pemeliharaan')->change();
        });
    }
};
