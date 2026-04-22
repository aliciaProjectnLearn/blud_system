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
        Schema::create('produk_servis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->enum('tipe_kendaraan', ['motor', 'mobil']);
            $table->decimal('harga', 12, 2);
            $table->integer('stok')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('kode_part')->nullable();
            $table->string('merk')->nullable();
            $table->string('satuan')->default('pcs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_servis');
    }
};
