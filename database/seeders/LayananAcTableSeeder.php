<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LayananAc;
use App\Models\Kategori;

class LayananAcTableSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = Kategori::firstOrCreate(
            ['nama' => 'Service AC'],
            ['harga' => 0]
        );

        $layanans = [

            // 🔹 Jasa dasar
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Cek Kerusakan AC',
                'kapasitas_ac' => '-',
                'harga_jasa' => 35000,
            ],

            // 🔹 Cleaning
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Cuci AC Rumah',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 75000,
            ],
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Cuci AC Cassette',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 350000,
            ],
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Cuci AC Duct',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 500000,
            ],

            // 🔹 Freon
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Isi Freon R22',
                'kapasitas_ac' => '-',
                'harga_jasa' => 100000,
            ],
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Isi Freon R32',
                'kapasitas_ac' => '-',
                'harga_jasa' => 150000,
            ],

            // 🔹 Bongkar
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Bongkar AC',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 200000,
            ],
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Bongkar AC',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 350000,
            ],

            // 🔹 Pasang
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Pasang AC',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 300000,
            ],
            [
                'kategori_id' => $kategori->id,
                'nama' => 'Pasang AC',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 350000,
            ],
        ];

        foreach ($layanans as $layanan) {
            LayananAc::updateOrCreate(
                [
                    'nama' => $layanan['nama'],
                    'kapasitas_ac' => $layanan['kapasitas_ac']
                ],
                $layanan
            );
        }
    }
}