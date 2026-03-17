<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom is_cancelled dan catatan_batal di sewa_ruko
        // Pembatalan hanya bisa dilakukan saat status masih 'menunggu' atau 'pending'
        // Logika validasi ini ditangani di level Controller/Service,
        // tapi kita tambah kolom pendukungnya di sini

        Schema::table('sewa_ruko', function (Blueprint $table) {
            if (!Schema::hasColumn('sewa_ruko', 'tgl_batal')) {
                $table->dateTime('tgl_batal')->nullable()->after('status')
                      ->comment('Hanya diisi jika dibatalkan sebelum sewa aktif');
            }
            if (!Schema::hasColumn('sewa_ruko', 'alasan_batal')) {
                $table->string('alasan_batal')->nullable()->after('tgl_batal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sewa_ruko', function (Blueprint $table) {
            if (Schema::hasColumn('sewa_ruko', 'tgl_batal')) {
                $table->dropColumn('tgl_batal');
            }
            if (Schema::hasColumn('sewa_ruko', 'alasan_batal')) {
                $table->dropColumn('alasan_batal');
            }
        });
    }
};
