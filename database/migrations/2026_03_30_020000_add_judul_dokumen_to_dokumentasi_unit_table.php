<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumentasi_unit', function (Blueprint $table) {
            // Tambah kolom judul_dokumen setelah kolom tipe
            $table->string('judul_dokumen')->nullable()->after('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('dokumentasi_unit', function (Blueprint $table) {
            $table->dropColumn('judul_dokumen');
        });
    }
};
