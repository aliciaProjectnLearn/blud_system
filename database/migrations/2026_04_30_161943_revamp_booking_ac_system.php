<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_ac', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            if (!Schema::hasColumn('booking_ac', 'nama_pelanggan')) {
                $table->string('nama_pelanggan')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('booking_ac', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('nama_pelanggan');
            }
            if (!Schema::hasColumn('booking_ac', 'foto_hasil')) {
                $table->string('foto_hasil')->nullable()->after('status');
            }
        });

        // Pastikan tabel booking (parent) juga membolehkan user_id null untuk tamu
        Schema::table('booking', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        Schema::table('detail_servis', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_servis', 'tipe')) {
                $table->enum('tipe', ['layanan', 'sparepart'])->default('layanan')->after('booking_id');
            }
            if (!Schema::hasColumn('detail_servis', 'layanan_id')) {
                $table->foreignId('layanan_id')->nullable()->constrained('layanan_ac')->after('tipe');
            }
            if (!Schema::hasColumn('detail_servis', 'produk_id')) {
                $table->foreignId('produk_id')->nullable()->constrained('produks')->after('layanan_id');
            }
        });

        Schema::table('pengeluaran_ac', function (Blueprint $table) {
            if (!Schema::hasColumn('pengeluaran_ac', 'kategori')) {
                $table->string('kategori')->nullable()->after('nominal');
            }
        });

        Schema::table('penggajian_teknisi', function (Blueprint $table) {
            if (!Schema::hasColumn('penggajian_teknisi', 'booking_ac_id')) {
                $table->foreignId('booking_ac_id')->nullable()->constrained('booking_ac')->after('booking_servis_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_ac', function (Blueprint $table) {
            $table->dropColumn(['nama_pelanggan', 'no_hp', 'foto_hasil']);
        });

        Schema::table('detail_servis', function (Blueprint $table) {
            $table->dropForeign(['layanan_id']);
            $table->dropForeign(['produk_id']);
            $table->dropColumn(['tipe', 'layanan_id', 'produk_id']);
        });

        Schema::table('pengeluaran_ac', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });

        Schema::table('penggajian_teknisi', function (Blueprint $table) {
            $table->dropForeign(['booking_ac_id']);
            $table->dropColumn('booking_ac_id');
        });
    }
};
