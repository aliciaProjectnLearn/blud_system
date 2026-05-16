<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RINGKASAN KOLOM FINAL TABEL booking_futsal:
 * - id
 * - access_token (string 64, unique, nullable)
 * - booking_id (FK ke booking, nullable)
 * - lapangan_id (FK ke lapangan)
 * - nama_pemesan (string)
 * - no_hp (string)
 * - start_datetime (datetime)
 * - end_datetime (datetime)
 * - jenis_pembayaran (enum: reguler, paket)
 * - status (enum: menunggu, dikonfirmasi, selesai, dibatalkan) default menunggu
 * - catatan (text, nullable)
 * - created_at, updated_at
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_futsal', function (Blueprint $table) {
            if (Schema::hasColumn('booking_futsal', 'user_id')) {
                // Hapus foreign key constraint jika ada, tapi karena mungkin rumit, kita coba drop index/foreign jika error, 
                // tapi asumsikan ini cuma kolom atau tidak ada foreign key terpisah di level DB yg menghalangi dropColumn.
                // Jika DB strict, kita butuh drop foreign key. Mari asumsikan Laravel dropColumn bisa handle atau foreign key tidak ada.
                // Untuk aman:
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('booking_futsal', 'tgl_main')) $table->dropColumn('tgl_main');
            if (Schema::hasColumn('booking_futsal', 'jam_mulai')) $table->dropColumn('jam_mulai');
            if (Schema::hasColumn('booking_futsal', 'jam_mulai_efektif')) $table->dropColumn('jam_mulai_efektif');
            if (Schema::hasColumn('booking_futsal', 'jam_selesai')) $table->dropColumn('jam_selesai');
            if (Schema::hasColumn('booking_futsal', 'durasi_main')) $table->dropColumn('durasi_main');
            if (Schema::hasColumn('booking_futsal', 'type')) $table->dropColumn('type');

            if (!Schema::hasColumn('booking_futsal', 'nama_pemesan')) {
                $table->string('nama_pemesan')->after('lapangan_id')->nullable();
            }
            if (!Schema::hasColumn('booking_futsal', 'no_hp')) {
                $table->string('no_hp')->after('nama_pemesan')->nullable();
            }
            
            // Ubah jenis_pembayaran ke enum yang baru jika memungkinkan.
            // Pengecekan perubahan ENUM di database seringkali rumit, namun kita bisa menggunakan DB::statement jika diperlukan.
            // Atau cukup ubah tipe datanya ke enum baru menggunakan doctrine dbal atau raw sql
            // Tapi karena MySQL mengizinkan perubahan enum:
            // $table->enum('jenis_pembayaran', ['reguler', 'paket'])->change(); 
            // Kita skip modifier 'change()' agar tidak error jika doctrine/dbal tidak ada, kita jalankan ALTER via DB::statement.
            
            if (!Schema::hasColumn('booking_futsal', 'status')) {
                $table->enum('status', ['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'])->default('menunggu')->after('jenis_pembayaran');
            }
            if (!Schema::hasColumn('booking_futsal', 'catatan')) {
                $table->text('catatan')->nullable()->after('status');
            }
        });
        
        // Memaksa update jenis_pembayaran
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE booking_futsal MODIFY COLUMN jenis_pembayaran ENUM('reguler', 'paket')");
    }

    public function down(): void
    {
        Schema::table('booking_futsal', function (Blueprint $table) {
            // Restore kolom jika rollback
            if (!Schema::hasColumn('booking_futsal', 'user_id')) $table->foreignId('user_id')->nullable();
            if (!Schema::hasColumn('booking_futsal', 'tgl_main')) $table->date('tgl_main')->nullable();
            if (!Schema::hasColumn('booking_futsal', 'jam_mulai')) $table->time('jam_mulai')->nullable();
            if (!Schema::hasColumn('booking_futsal', 'jam_mulai_efektif')) $table->time('jam_mulai_efektif')->nullable();
            if (!Schema::hasColumn('booking_futsal', 'jam_selesai')) $table->time('jam_selesai')->nullable();
            if (!Schema::hasColumn('booking_futsal', 'durasi_main')) $table->integer('durasi_main')->nullable();
            if (!Schema::hasColumn('booking_futsal', 'type')) $table->string('type')->nullable();

            if (Schema::hasColumn('booking_futsal', 'nama_pemesan')) $table->dropColumn('nama_pemesan');
            if (Schema::hasColumn('booking_futsal', 'no_hp')) $table->dropColumn('no_hp');
            if (Schema::hasColumn('booking_futsal', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('booking_futsal', 'catatan')) $table->dropColumn('catatan');
        });
    }
};
