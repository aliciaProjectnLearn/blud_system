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
        Schema::table('pembayaran_futsal', function (Blueprint $table) {
            $table->string('kode_pembayaran')->unique()->after('id');
            $table->enum('jenis_transaksi', ['membership', 'booking', 'guest'])->default('booking')->after('kode_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_futsal', function (Blueprint $table) {
            $table->dropColumn(['kode_pembayaran', 'jenis_transaksi']);
        });
    }
};
