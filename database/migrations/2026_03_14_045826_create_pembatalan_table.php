<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembatalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('booking')->onDelete('cascade');
            $table->string('alasan')->nullable();
            $table->date('tgl_pembatalan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembatalan');
    }
};
