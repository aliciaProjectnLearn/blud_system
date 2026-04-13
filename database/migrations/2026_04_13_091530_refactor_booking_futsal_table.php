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
        Schema::table('booking_futsal', function (Blueprint $table) {
            // Tambah kolom baru
            $table->datetime('start_datetime')->nullable()->after('lapangan_id');
            $table->datetime('end_datetime')->nullable()->after('start_datetime');
            $table->enum('type', ['regular', 'event'])->default('regular')->after('end_datetime');

            // Migrasi data lama ke kolom baru
            // (akan dihandle manual setelah migrate)

            // Hapus kolom lama
            $table->dropColumn([
                'tgl_main',
                'jam_mulai',
                'jam_mulai_efektif',
                'jam_selesai',
                'durasi_main',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('booking_futsal', function (Blueprint $table) {
            $table->date('tgl_main')->after('lapangan_id');
            $table->time('jam_mulai')->after('tgl_main');
            $table->time('jam_mulai_efektif')->nullable()->after('jam_mulai');
            $table->time('jam_selesai')->after('jam_mulai_efektif');
            $table->integer('durasi_main')->default(1)->after('jam_selesai');
            $table->dropColumn(['start_datetime', 'end_datetime', 'type']);
        });
    }
};
