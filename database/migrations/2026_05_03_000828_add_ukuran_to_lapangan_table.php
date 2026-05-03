<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            // Tambahkan kembali kolom ukuran yang sebelumnya dihapus
            if (!Schema::hasColumn('lapangan', 'ukuran')) {
                $table->string('ukuran')->nullable()->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            if (Schema::hasColumn('lapangan', 'ukuran')) {
                $table->dropColumn('ukuran');
            }
        });
    }
};
