<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->enum('status_unit', ['terisi', 'kosong'])->default('kosong');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruko');
    }
};
