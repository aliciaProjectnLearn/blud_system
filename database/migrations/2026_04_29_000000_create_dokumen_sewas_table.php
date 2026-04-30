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
        Schema::create('dokumen_sewas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sewa_ruko_id')->constrained('sewa_ruko')->onDelete('cascade');
            $table->string('tipe_dokumen'); // mou_sistem, mou_hardfile, dokumen_lain
            $table->string('nama_dokumen');
            $table->string('path_file');
            $table->string('diunggah_oleh')->default('sistem'); // sistem / admin
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_sewas');
    }
};
