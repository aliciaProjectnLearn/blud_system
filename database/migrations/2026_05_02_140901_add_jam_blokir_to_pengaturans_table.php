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
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->boolean('jam_blokir_aktif')->default(false)->after('harga_event_futsal');
            $table->time('jam_blokir_mulai')->default('07:00:00')->after('jam_blokir_aktif');
            $table->time('jam_blokir_selesai')->default('15:00:00')->after('jam_blokir_mulai');
            $table->string('hari_blokir')->default('Senin,Selasa,Rabu,Kamis,Jumat')->after('jam_blokir_selesai');
            $table->string('keterangan_blokir')->nullable()->after('hari_blokir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->dropColumn([
                'jam_blokir_aktif',
                'jam_blokir_mulai',
                'jam_blokir_selesai',
                'hari_blokir',
                'keterangan_blokir'
            ]);
        });
    }
};

