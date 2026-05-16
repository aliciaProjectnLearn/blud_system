<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruko', function (Blueprint $table) {
            if (!Schema::hasColumn('ruko', 'nama_ruko')) {
                $table->string('nama_ruko')->after('id')->nullable();
            }
            if (!Schema::hasColumn('ruko', 'ukuran_ruko')) {
                $table->string('ukuran_ruko')->after('nama_ruko')->nullable();
            }
            if (!Schema::hasColumn('ruko', 'foto_ruko')) {
                $table->string('foto_ruko')->after('ukuran_ruko')->nullable();
            }
            if (!Schema::hasColumn('ruko', 'posisi_x')) {
                $table->float('posisi_x')->after('foto_ruko')->nullable();
            }
            if (!Schema::hasColumn('ruko', 'posisi_y')) {
                $table->float('posisi_y')->after('posisi_x')->nullable();
            }
            if (!Schema::hasColumn('ruko', 'status')) {
                $table->enum('status', ['tersedia', 'disewa', 'tidak_tersedia'])->default('tersedia')->after('posisi_y');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ruko', function (Blueprint $table) {
            $table->dropColumn(['nama_ruko', 'ukuran_ruko', 'foto_ruko', 'posisi_x', 'posisi_y', 'status']);
        });
    }
};
