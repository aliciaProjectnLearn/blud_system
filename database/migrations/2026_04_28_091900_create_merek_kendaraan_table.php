<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merek_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['motor', 'mobil']);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merek_kendaraan');
    }
};
