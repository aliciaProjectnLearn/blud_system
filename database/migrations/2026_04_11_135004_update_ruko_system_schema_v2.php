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
        // Add sewa_ruko_id to pembayaran_ruko
        Schema::table('pembayaran_ruko', function (Blueprint $table) {
            $table->foreignId('sewa_ruko_id')->nullable()->after('id')->constrained('sewa_ruko')->onDelete('cascade');
            
            // Modify status enum
            DB::statement("ALTER TABLE pembayaran_ruko MODIFY COLUMN status ENUM('menunggu', 'verifikasi', 'lunas', 'dibatalkan') DEFAULT 'menunggu'");
        });

        // Add notifikasi_terkirim to sewa_ruko
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->boolean('notifikasi_terkirim')->default(false)->after('status');
        });

        // Add qris_path to kategori
        Schema::table('kategori', function (Blueprint $table) {
            $table->string('qris_path')->nullable()->after('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_ruko', function (Blueprint $table) {
            $table->dropForeign(['sewa_ruko_id']);
            $table->dropColumn('sewa_ruko_id');
            DB::statement("ALTER TABLE pembayaran_ruko MODIFY COLUMN status ENUM('menunggu', 'verifikasi', 'dibatalkan') DEFAULT 'menunggu'");
        });

        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->dropColumn('notifikasi_terkirim');
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->dropColumn('qris_path');
        });
    }
};
