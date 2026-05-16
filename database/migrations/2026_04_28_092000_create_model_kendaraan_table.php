<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merek_kendaraan_id')->constrained('merek_kendaraan')->cascadeOnDelete();
            $table->string('nama_model');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_kendaraan');
    }
};
