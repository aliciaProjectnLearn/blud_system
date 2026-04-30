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
        Schema::table('dokumentasi_unit', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('judul_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumentasi_unit', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }
};
