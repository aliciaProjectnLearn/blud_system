<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Tambah NIK ke users sebagai nullable
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 20)->nullable()->after('no_hp');
            }
        });

        // Step 2: Hapus NIK dari tabel penyewa
        Schema::table('penyewa', function (Blueprint $table) {
            if (Schema::hasColumn('penyewa', 'nik')) {
                $table->dropColumn('nik');
            }
        });
    }

    public function down(): void
    {
        // Kembalikan NIK ke penyewa
        Schema::table('penyewa', function (Blueprint $table) {
            if (!Schema::hasColumn('penyewa', 'nik')) {
                $table->string('nik')->after('alamat');
            }
        });

        // Hapus NIK dari users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nik')) {
                $table->dropColumn('nik');
            }
        });
    }
};
