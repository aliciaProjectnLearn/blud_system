<?php

namespace Database\Seeders;

use App\Models\KategoriKomponen;
use App\Models\Produk;
use App\Models\ProdukServis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriMapping = KategoriKomponen::pluck('id', 'nama');

        $produks = [
            [
                'nama_produk' => 'Pipa Tembaga Artic 1/4',
                'id_kategori_komponen' => $kategoriMapping['Pipa AC'] ?? 1,
                'satuan' => 'Meter',
                'harga' => 150000,
                'stok' => 20,
                'deskripsi' => 'Pipa tembaga berkualitas tinggi untuk perumahan'
            ],
            [
                'nama_produk' => 'Freon R32',
                'id_kategori_komponen' => $kategoriMapping['Freon'] ?? 2,
                'satuan' => 'Tabung',
                'harga' => 350000,
                'stok' => 3,
                'deskripsi' => 'Freon ramah lingkungan'
            ],
            [
                'nama_produk' => 'Kabel NYM 2x1.5',
                'id_kategori_komponen' => $kategoriMapping['Kabel'] ?? 3,
                'satuan' => 'Meter',
                'harga' => 25000,
                'stok' => 50,
                'deskripsi' => 'Kabel listrik standar SNI'
            ],
            [
                'nama_produk' => 'Freon R410A',
                'id_kategori_komponen' => $kategoriMapping['Freon'] ?? 2,
                'satuan' => 'Tabung',
                'harga' => 450000,
                'stok' => 5,
                'deskripsi' => 'Freon untuk AC tipe inverter'
            ],
            [
                'nama_produk' => 'Kapasitor 1.5 uF',
                'id_kategori_komponen' => $kategoriMapping['Kapasitor'] ?? 5,
                'satuan' => 'Pcs',
                'harga' => 35000,
                'stok' => 30,
                'deskripsi' => 'Kapasitor kipas indoor/outdoor AC'
            ],
            [
                'nama_produk' => 'Kapasitor 20 uF',
                'id_kategori_komponen' => $kategoriMapping['Kapasitor'] ?? 5,
                'satuan' => 'Pcs',
                'harga' => 75000,
                'stok' => 15,
                'deskripsi' => 'Kapasitor kompresor AC 1/2 PK'
            ],
            [
                'nama_produk' => 'Bracket AC 1/2 - 1 PK',
                'id_kategori_komponen' => $kategoriMapping['Bracket AC'] ?? 6,
                'satuan' => 'Pasang',
                'harga' => 60000,
                'stok' => 25,
                'deskripsi' => 'Bracket outdoor AC bahan tebal berkualitas'
            ],
            [
                'nama_produk' => 'Duct Tape / Lakban AC',
                'id_kategori_komponen' => $kategoriMapping['Material Umum'] ?? 7,
                'satuan' => 'Roll',
                'harga' => 15000,
                'stok' => 100,
                'deskripsi' => 'Duct tape non-lem pembungkus pipa AC'
            ],
            [
                'nama_produk' => 'Dinamo Motor Indoor AC',
                'id_kategori_komponen' => $kategoriMapping['Sparepart'] ?? 4,
                'satuan' => 'Pcs',
                'harga' => 250000,
                'stok' => 2,
                'deskripsi' => 'Motor fan indoor universal'
            ],
            [
                'nama_produk' => 'Termistor AC Daikin',
                'id_kategori_komponen' => $kategoriMapping['Sparepart'] ?? 4,
                'satuan' => 'Pcs',
                'harga' => 85000,
                'stok' => 10,
                'deskripsi' => 'Sensor suhu termistor AC Daikin'
            ],
            [
                'nama_produk' => 'Remote AC Universal',
                'id_kategori_komponen' => $kategoriMapping['Sparepart'] ?? 4,
                'satuan' => 'Pcs',
                'harga' => 50000,
                'stok' => 20,
                'deskripsi' => 'Remote multi untuk berbagai merk AC'
            ]
        ];

        foreach ($produks as $produk) {
            Produk::updateOrCreate(
                ['nama_produk' => $produk['nama_produk']],
                $produk
            );
        }

        // Seed Produk Servis Kendaraan
        $produkServis = [
            ['nama_produk' => 'Oli Mesin Castrol 10W-40', 'tipe_kendaraan' => 'mobil', 'harga' => 350000, 'stok' => 20, 'satuan' => 'Pcs'],
            ['nama_produk' => 'Oli Mesin Yamalube Sport', 'tipe_kendaraan' => 'motor', 'harga' => 65000, 'stok' => 50, 'satuan' => 'Pcs'],
            ['nama_produk' => 'Kampas Rem Depan Honda Beat', 'tipe_kendaraan' => 'motor', 'harga' => 45000, 'stok' => 15, 'satuan' => 'Pcs'],
            ['nama_produk' => 'Filter Udara Avanza/Xenia', 'tipe_kendaraan' => 'mobil', 'harga' => 125000, 'stok' => 10, 'satuan' => 'Pcs'],
            ['nama_produk' => 'Busi NGK CPR9EA-9', 'tipe_kendaraan' => 'motor', 'harga' => 25000, 'stok' => 100, 'satuan' => 'Pcs']
        ];

        foreach ($produkServis as $ps) {
            ProdukServis::updateOrCreate(
                ['nama_produk' => $ps['nama_produk']],
                $ps
            );
        }
    }
}
