<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lapangan', function(Blueprint $table) {
            if (!Schema::hasColumn('lapangan', 'lokasi')) {
                $table->string('lokasi')->nullable()->after('spesifikasi'); // 'spesifikasi' mungkin tidak ada, tapi ok kita asumsikan setelah deskripsi atau diletakkan saja di akhir
            }
        });
    }

    public function down(): void
    {
        Schema::table('lapangan', function(Blueprint $table) {
            if (Schema::hasColumn('lapangan', 'lokasi')) {
                $table->dropColumn('lokasi');
            }
        });
    }
};
