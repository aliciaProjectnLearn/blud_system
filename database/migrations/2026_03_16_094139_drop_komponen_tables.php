<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus detail_komponen_servis dulu karena ada FK ke komponen
        Schema::dropIfExists('detail_komponen_servis');

        // Baru hapus komponen
        Schema::dropIfExists('komponen');
    }

    public function down(): void
    {
        // Buat ulang komponen
        Schema::create('komponen', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis_komponen')->nullable();
            $table->integer('harga');
            $table->timestamps();
        });

        // Buat ulang detail_komponen_servis
        Schema::create('detail_komponen_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_ac_id') ->constrained('pembayaran_ac')->onDelete('cascade');
            $table->foreignId('komponen_id')->constrained('komponen')->onDelete('restrict');
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }
};
