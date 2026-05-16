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
        Schema::table('pengeluaran_ac', function (Blueprint $table) {
            $table->enum('kategori', ['sparepart', 'gaji', 'lainnya'])->default('lainnya')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluaran_ac', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
