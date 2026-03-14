<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewa', function (Blueprint $table) {
            $table->id(); // id (primary key)

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // relasi ke tabel users

            $table->string('nama_usaha'); // varchar
            $table->text('alamat'); // text
            $table->string('nik'); // varchar

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewa');
    }
};