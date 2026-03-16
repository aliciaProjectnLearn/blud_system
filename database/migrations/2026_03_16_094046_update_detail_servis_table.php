<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_servis', function (Blueprint $table) {
            // Hapus kolom freon yang lama
            if (Schema::hasColumn('detail_servis', 'tambah_freon')) {
                $table->dropColumn('tambah_freon');
            }
            if (Schema::hasColumn('detail_servis', 'berat_freon_kg')) {
                $table->dropColumn('berat_freon_kg');
            }
            if (Schema::hasColumn('detail_servis', 'jasa_cek')) {
                $table->dropColumn('jasa_cek');
            }

            // Kolom baru: item (nama jasa/komponen manual input), satuan, quantity
            if (!Schema::hasColumn('detail_servis', 'item')) {
                $table->string('item')->after('booking_id')
                      ->comment('Nama jasa atau komponen, input manual');
            }
            if (!Schema::hasColumn('detail_servis', 'satuan')) {
                $table->string('satuan')->nullable()->after('item')
                      ->comment('Contoh: pcs, liter, kg, unit');
            }
            if (!Schema::hasColumn('detail_servis', 'quantity')) {
                $table->decimal('quantity', 8, 2)->default(1)->after('satuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_servis', function (Blueprint $table) {
            if (Schema::hasColumn('detail_servis', 'item')) {
                $table->dropColumn('item');
            }
            if (Schema::hasColumn('detail_servis', 'satuan')) {
                $table->dropColumn('satuan');
            }
            if (Schema::hasColumn('detail_servis', 'quantity')) {
                $table->dropColumn('quantity');
            }

            // Kembalikan kolom lama
            $table->string('jasa_cek')->nullable()->after('booking_id');
            $table->decimal('tambah_freon', 8, 2)->nullable()->after('jasa_cek');
            $table->decimal('berat_freon_kg', 8, 2)->nullable()->after('tambah_freon');
        });
    }
};
