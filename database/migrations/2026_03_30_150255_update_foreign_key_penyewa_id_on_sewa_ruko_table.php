<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop foreign key lama yang menunjuk ke tabel users
        try {
            Schema::table('sewa_ruko', function (Blueprint $table) {
                $table->dropForeign(['penyewa_id']);
                // Catatan: Nama default constraint Laravel adalah namaTabel_namaKolom_foreign
                // yaitu: sewa_ruko_penyewa_id_foreign
            });
        } catch (\Exception $e) {
            // Abaikan jika FK sudah terlanjur di-drop pada percobaan migrate sebelumnya yang gagal
        }

        // 2. Transformasi Value / Sinkronisasi Data (Sangat Krusial!)
        // Mengubah isi kolom `penyewa_id` yang tadinya berisi ID dari tabel `users`,
        // menjadi ID asli dari tabel `penyewa` agar terhindar dari Integrity Constraint Violation saat FK baru dipasang.
        DB::statement('
            UPDATE sewa_ruko 
            JOIN penyewa ON sewa_ruko.penyewa_id = penyewa.user_id 
            SET sewa_ruko.penyewa_id = penyewa.id
        ');

        DB::statement('
            DELETE FROM sewa_ruko 
            WHERE penyewa_id NOT IN (SELECT id FROM penyewa)
        ');

        // 3. Tambahkan foreign key baru yang menunjuk ke id tabel penyewa
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->foreign('penyewa_id')
                  ->references('id')->on('penyewa')
                  ->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->dropForeign(['penyewa_id']);
        });

        // Kembalikan value `penyewa_id` ke ID users
        DB::statement('
            UPDATE sewa_ruko 
            JOIN penyewa ON sewa_ruko.penyewa_id = penyewa.id 
            SET sewa_ruko.penyewa_id = penyewa.user_id
        ');

        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->foreign('penyewa_id')
                  ->references('id')->on('users')
                  ->onDelete('restrict');
        });
    }
};
