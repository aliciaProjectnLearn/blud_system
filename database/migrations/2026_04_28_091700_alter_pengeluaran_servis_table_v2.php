<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah ke tipe VARCHAR sementara agar tidak error saat update data
        DB::statement("ALTER TABLE pengeluaran_servis MODIFY COLUMN kategori VARCHAR(255) NOT NULL");

        // 2. Map data lama ke nilai ENUM yang baru
        DB::table('pengeluaran_servis')->where('kategori', 'sparepart')->update(['kategori' => 'sparepart_produk']);
        DB::table('pengeluaran_servis')->where('kategori', 'operasional')->update(['kategori' => 'gaji_teknisi']);
        DB::table('pengeluaran_servis')->where('kategori', 'lainnya')->update(['kategori' => 'gaji_kasir']);

        // 3. (Safety) Jika masih ada nilai yang tidak dikenali, set ke default 'sparepart_produk'
        DB::table('pengeluaran_servis')
            ->whereNotIn('kategori', ['sparepart_produk', 'gaji_teknisi', 'gaji_kasir'])
            ->update(['kategori' => 'sparepart_produk']);

        // 4. Ubah struktur kolom menjadi ENUM dengan opsi baru
        DB::statement("ALTER TABLE pengeluaran_servis MODIFY COLUMN kategori ENUM('sparepart_produk', 'gaji_teknisi', 'gaji_kasir') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengeluaran_servis MODIFY COLUMN kategori VARCHAR(255) NOT NULL");

        DB::table('pengeluaran_servis')->where('kategori', 'sparepart_produk')->update(['kategori' => 'sparepart']);
        DB::table('pengeluaran_servis')->where('kategori', 'gaji_teknisi')->update(['kategori' => 'operasional']);
        DB::table('pengeluaran_servis')->where('kategori', 'gaji_kasir')->update(['kategori' => 'lainnya']);

        DB::statement("ALTER TABLE pengeluaran_servis MODIFY COLUMN kategori ENUM('sparepart', 'operasional', 'lainnya') NOT NULL");
    }
};
