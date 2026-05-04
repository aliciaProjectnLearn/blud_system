<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LayananAc;
use App\Models\Kategori;

class LayananAcTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definisikan Kategori-kategori AC
        $kategoriData = [
            ['nama' => 'JASA UMUM', 'tipe' => 'ac'],
            ['nama' => 'CLEANING AC', 'tipe' => 'ac'],
            ['nama' => 'FREON / REFRIGERANT', 'tipe' => 'ac'],
            ['nama' => 'BONGKAR / PASANG AC', 'tipe' => 'ac'],
            ['nama' => 'KOMPONEN ELEKTRIK AC', 'tipe' => 'ac'],
            ['nama' => 'KOMPONEN MEKANIK AC', 'tipe' => 'ac'],
        ];

        $kategoriMap = [];
        foreach ($kategoriData as $kd) {
            $kategoriMap[$kd['nama']] = Kategori::updateOrCreate(
                ['nama' => $kd['nama']],
                $kd
            ); 
        }

        // 2. Definisikan Layanan AC berdasarkan data user
        $layanans = [
            // JASA UMUM
            [
                'kategori_id' => $kategoriMap['JASA UMUM']->id,
                'nama' => 'Jasa kunjungan dan cek kerusakan',
                'kapasitas_ac' => null,
                'harga_jasa' => 35000,
            ],

            // CLEANING AC
            [
                'kategori_id' => $kategoriMap['CLEANING AC']->id,
                'nama' => 'Cleaning service AC rumah tangga',
                'kapasitas_ac' => null,
                'harga_jasa' => 75000,
            ],
            [
                'kategori_id' => $kategoriMap['CLEANING AC']->id,
                'nama' => 'Cleaning service AC Cassette',
                'kapasitas_ac' => null,
                'harga_jasa' => 350000,
            ],
            [
                'kategori_id' => $kategoriMap['CLEANING AC']->id,
                'nama' => 'Cleaning service AC Duct Split',
                'kapasitas_ac' => null,
                'harga_jasa' => 500000,
            ],

            // FREON / REFRIGERANT
            [
                'kategori_id' => $kategoriMap['FREON / REFRIGERANT']->id,
                'nama' => 'Tambah freon R22 per kg',
                'kapasitas_ac' => null,
                'harga_jasa' => 100000,
            ],
            [
                'kategori_id' => $kategoriMap['FREON / REFRIGERANT']->id,
                'nama' => 'Tambah freon R32/R410 per kg',
                'kapasitas_ac' => null,
                'harga_jasa' => 150000,
            ],

            // BONGKAR / PASANG AC
            [
                'kategori_id' => $kategoriMap['BONGKAR / PASANG AC']->id,
                'nama' => 'Bongkar AC 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 200000,
            ],
            [
                'kategori_id' => $kategoriMap['BONGKAR / PASANG AC']->id,
                'nama' => 'Bongkar AC 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 350000,
            ],
            [
                'kategori_id' => $kategoriMap['BONGKAR / PASANG AC']->id,
                'nama' => 'Pasang AC 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 300000,
            ],
            [
                'kategori_id' => $kategoriMap['BONGKAR / PASANG AC']->id,
                'nama' => 'Pasang AC 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 350000,
            ],

            // KOMPONEN ELEKTRIK AC
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti kapasitor compressor 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 200000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti kapasitor compressor 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 250000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti kapasitor fan 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 100000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti kapasitor fan 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 150000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti thermistor double',
                'kapasitas_ac' => null,
                'harga_jasa' => 150000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti thermistor single',
                'kapasitas_ac' => null,
                'harga_jasa' => 120000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti sensor remote',
                'kapasitas_ac' => null,
                'harga_jasa' => 250000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti overload protector',
                'kapasitas_ac' => null,
                'harga_jasa' => 100000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti motor swing',
                'kapasitas_ac' => null,
                'harga_jasa' => 100000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti motor blower',
                'kapasitas_ac' => null,
                'harga_jasa' => 250000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti modul AC universal 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 300000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN ELEKTRIK AC']->id,
                'nama' => 'Ganti modul AC universal 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 500000,
            ],

            // KOMPONEN MEKANIK AC
            [
                'kategori_id' => $kategoriMap['KOMPONEN MEKANIK AC']->id,
                'nama' => 'Ganti compressor AC 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 300000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN MEKANIK AC']->id,
                'nama' => 'Ganti compressor AC 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 550000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN MEKANIK AC']->id,
                'nama' => 'Ganti evaporator AC 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 300000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN MEKANIK AC']->id,
                'nama' => 'Ganti evaporator AC 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 550000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN MEKANIK AC']->id,
                'nama' => 'Ganti condenser AC 0.5 - 1 PK',
                'kapasitas_ac' => '0.5 - 1 PK',
                'harga_jasa' => 300000,
            ],
            [
                'kategori_id' => $kategoriMap['KOMPONEN MEKANIK AC']->id,
                'nama' => 'Ganti condenser AC 1.5 - 2 PK',
                'kapasitas_ac' => '1.5 - 2 PK',
                'harga_jasa' => 550000,
            ],
        ];

        // 3. Masukkan data layanan
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