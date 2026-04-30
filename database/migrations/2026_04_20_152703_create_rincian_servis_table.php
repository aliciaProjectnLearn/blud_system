<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rincian_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_servis_id')->constrained('booking_servis')->cascadeOnDelete();
            $table->foreignId('kategori_komponen_id')->nullable()->constrained('kategori_komponens')->nullOnDelete();
            $table->string('nama_item');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_servis');
    }
};
