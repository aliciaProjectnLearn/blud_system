<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_servis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_estimasi', 15, 2)->nullable();
            $table->enum('tipe_kendaraan', ['motor', 'mobil', 'keduanya'])->default('keduanya');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_servis');
    }
};
