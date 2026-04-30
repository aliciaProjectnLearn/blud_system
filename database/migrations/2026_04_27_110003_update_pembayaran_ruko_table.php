<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_ruko', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran_ruko', 'sewa_ruko_id')) {
                $table->foreignId('sewa_ruko_id')->nullable()->after('id')->constrained('sewa_ruko')->onDelete('cascade');
            }
            if (!Schema::hasColumn('pembayaran_ruko', 'termin_ke')) {
                $table->tinyInteger('termin_ke')->nullable()->after('termin');
            }
            if (!Schema::hasColumn('pembayaran_ruko', 'jumlah_bayar')) {
                $table->decimal('jumlah_bayar', 15, 2)->nullable()->after('jumlah_tagihan');
            }
            if (!Schema::hasColumn('pembayaran_ruko', 'tipe_pembayaran')) {
                $table->enum('tipe_pembayaran', ['transfer', 'tunai'])->nullable()->after('tipe_pembayaran_id');
            }
            if (!Schema::hasColumn('pembayaran_ruko', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['pending', 'menunggu_verifikasi', 'dibayar', 'ditolak'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('pembayaran_ruko', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable()->after('path_bukti');
            }
            if (!Schema::hasColumn('pembayaran_ruko', 'tanggal_bayar')) {
                $table->date('tanggal_bayar')->nullable()->after('tgl_bayar');
            }
            if (!Schema::hasColumn('pembayaran_ruko', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('tanggal_bayar');
            }

            // Make legacy columns nullable
            $table->foreignId('booking_id')->nullable()->change();
            $table->foreignId('tipe_pembayaran_id')->nullable()->change();
            $table->integer('jumlah_tagihan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_ruko', function (Blueprint $table) {
            $table->dropForeign(['sewa_ruko_id']);
            $table->dropColumn([
                'sewa_ruko_id', 'termin_ke', 'jumlah_bayar', 'tipe_pembayaran_v2', 
                'status_pembayaran', 'bukti_pembayaran', 'tanggal_bayar_v2', 'catatan_admin'
            ]);
        });
    }
};
