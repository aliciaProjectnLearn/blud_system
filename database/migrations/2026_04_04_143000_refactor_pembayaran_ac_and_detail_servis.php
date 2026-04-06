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
        // 1. Refactor pembayaran_ac
        Schema::table('pembayaran_ac', function (Blueprint $table) {
            // Hapus kolom yang tidak relevan lagi
            if (Schema::hasColumn('pembayaran_ac', 'layanan_ac_id')) {
                $table->dropForeign(['layanan_ac_id']);
                $table->dropColumn('layanan_ac_id');
            }
            if (Schema::hasColumn('pembayaran_ac', 'total_harga_jasa')) {
                $table->dropColumn('total_harga_jasa');
            }
            if (Schema::hasColumn('pembayaran_ac', 'tgl_servis')) {
                $table->dropColumn('tgl_servis');
            }

            // Ubah total_biaya menjadi total_harga
            if (Schema::hasColumn('pembayaran_ac', 'total_biaya')) {
                $table->renameColumn('total_biaya', 'total_harga');
            }

            // Ubah Enum Status (Menunggu/Verifikasi -> Pending/Dibayar/Ditolak)
            $table->enum('status_new', ['pending', 'dibayar', 'ditolak'])->default('pending')->after('status');

            // Tambahkan Nomor Invoice
            if (!Schema::hasColumn('pembayaran_ac', 'invoice_no')) {
                $table->string('invoice_no')->unique()->nullable()->after('booking_id');
            }
        });

        // Migrasi data status lama ke baru
        DB::table('pembayaran_ac')->where('status', 'menunggu')->update(['status_new' => 'pending']);
        DB::table('pembayaran_ac')->where('status', 'verifikasi')->update(['status_new' => 'dibayar']);

        Schema::table('pembayaran_ac', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('pembayaran_ac', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });

        // 2. Refactor detail_servis
        Schema::table('detail_servis', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_servis', 'harga')) {
                $table->bigInteger('harga')->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('detail_servis', 'subtotal')) {
                $table->bigInteger('subtotal')->default(0)->after('harga');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_servis', function (Blueprint $table) {
            $table->dropColumn(['harga', 'subtotal']);
        });

        Schema::table('pembayaran_ac', function (Blueprint $table) {
            $table->renameColumn('total_harga', 'total_biaya');
            $table->enum('status_old', ['menunggu', 'verifikasi'])->default('menunggu')->after('status');
        });

        DB::table('pembayaran_ac')->where('status', 'pending')->update(['status_old' => 'menunggu']);
        DB::table('pembayaran_ac')->where('status', 'dibayar')->update(['status_old' => 'verifikasi']);

        Schema::table('pembayaran_ac', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('pembayaran_ac', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
            $table->unsignedBigInteger('layanan_ac_id')->nullable();
            $table->integer('total_harga_jasa')->default(0);
            $table->dateTime('tgl_servis')->nullable();
        });
    }
};
