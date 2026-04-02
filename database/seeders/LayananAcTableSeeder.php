<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LayananAc;
use App\Models\Kategori;

class LayananAcTableSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kategori Service AC
        $kategori = Kategori::where('nama', 'LIKE', '%Service AC%')->first();

        // Jika tidak ada, buat baru sebagai fallback
        if (!$kategori) {
            $kategori = Kategori::create([
                'nama' => 'Service AC',
                'harga' => 0
            ]);
        }

        $layanans = [
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Cleaning AC',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 75000,
            ],
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Tambah R22',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 100000,
            ],
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Bongkar AC',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 200000,
            ],
        ];

        foreach ($layanans as $layanan) {
            LayananAc::updateOrCreate(
                ['nama' => $layanan['nama'], 'kapasitas_ac' => $layanan['kapasitas_ac']],
                $layanan
            );
        }
    }
}