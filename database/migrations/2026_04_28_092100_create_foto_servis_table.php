<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_servis_id')->constrained('booking_servis')->cascadeOnDelete();
            $table->string('path_foto');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_servis');
    }
};
