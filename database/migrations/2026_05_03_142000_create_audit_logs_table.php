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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Pembeda sistem
            $table->enum('sistem', [
                'kantin',
                'futsal', 
                'ac',
                'servis',
                'global'    // untuk aksi lintas sistem / super admin
            ])->default('kantin')->index();

            $table->string('tabel_entitas');         // sewa_ruko, booking_futsal, dll
            $table->unsignedBigInteger('entitas_id')->nullable();
            $table->string('aksi');                  // booking_dibuat, status_diubah, dll
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->string('tipe_pelaku')->default('admin'); // admin / sistem
            $table->unsignedBigInteger('dilakukan_oleh')->nullable();
            $table->string('nama_pelaku')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Index untuk performa query
            $table->index(['sistem', 'tabel_entitas']);
            $table->index(['sistem', 'aksi']);
            $table->index(['sistem', 'created_at']);
            $table->index('dilakukan_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
