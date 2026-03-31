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
            Schema::create('tb_dokumen_penyewaan', function (Blueprint $table) {
        $table->id();

        $table->string('no_mou')->unique();

        $table->foreignId('sewa_id')
            ->constrained('sewa_ruko')
            ->cascadeOnDelete();

        $table->string('nama_dokumen');
        $table->string('path_file');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_dokumen_penyewaan');
    }
};
