<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            // Hapus ukuran, ganti dengan spesifikasi untuk deskripsi teknis lapangan
            if (Schema::hasColumn('lapangan', 'ukuran')) {
                $table->dropColumn('ukuran');
            }
            if (!Schema::hasColumn('lapangan', 'spesifikasi')) {
                $table->string('spesifikasi')->nullable()->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            if (Schema::hasColumn('lapangan', 'spesifikasi')) {
                $table->dropColumn('spesifikasi');
            }
            if (!Schema::hasColumn('lapangan', 'ukuran')) {
                $table->string('ukuran')->nullable()->after('nama');
            }
        });
    }
};
