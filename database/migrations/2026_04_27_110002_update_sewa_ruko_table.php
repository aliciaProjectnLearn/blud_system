<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sewa_ruko', function (Blueprint $table) {
            if (!Schema::hasColumn('sewa_ruko', 'access_token')) {
                $table->string('access_token', 64)->unique()->after('id');
            }
            if (!Schema::hasColumn('sewa_ruko', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('access_token')->constrained('users')->onDelete('restrict');
            }
            if (!Schema::hasColumn('sewa_ruko', 'nama_penyewa')) {
                $table->string('nama_penyewa')->nullable()->after('ruko_id');
            }
            if (!Schema::hasColumn('sewa_ruko', 'no_hp_snapshot')) {
                $table->string('no_hp_snapshot')->nullable()->after('nama_penyewa');
            }
            if (!Schema::hasColumn('sewa_ruko', 'nik_penyewa')) {
                $table->string('nik_penyewa', 16)->nullable()->after('no_hp_snapshot');
            }
            if (!Schema::hasColumn('sewa_ruko', 'status_sewa')) {
                $table->enum('status_sewa', ['pending', 'aktif', 'selesai', 'dibatalkan'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('sewa_ruko', 'tanggal_mulai_sewa')) {
                $table->date('tanggal_mulai_sewa')->nullable()->after('status_sewa');
            }
            if (!Schema::hasColumn('sewa_ruko', 'tanggal_selesai_sewa')) {
                $table->date('tanggal_selesai_sewa')->nullable()->after('tanggal_mulai_sewa');
            }
            if (!Schema::hasColumn('sewa_ruko', 'tipe_pembayaran')) {
                $table->enum('tipe_pembayaran', ['1_termin', '2_termin'])->nullable()->after('tanggal_selesai_sewa');
            }
            if (!Schema::hasColumn('sewa_ruko', 'catatan')) {
                $table->text('catatan')->nullable()->after('tipe_pembayaran');
            }

            // Make legacy columns nullable
            $table->foreignId('booking_id')->nullable()->change();
            $table->foreignId('penyewa_id')->nullable()->change();
            $table->date('tgl_mulai')->nullable()->change();
            $table->date('tgl_selesai')->nullable()->change();
            $table->integer('harga_sewa_tahunan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'access_token', 'user_id', 'nama_penyewa', 'no_hp_snapshot', 
                'nik_penyewa', 'status_sewa', 'tanggal_mulai_sewa', 
                'tanggal_selesai_sewa', 'tipe_pembayaran', 'catatan'
            ]);
        });
    }
};
