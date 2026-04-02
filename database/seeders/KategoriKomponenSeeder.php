<?php

namespace Database\Seeders;

use App\Models\KategoriKomponen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriKomponenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Pipa AC', 'harga' => 150000],
            ['nama' => 'Freon', 'harga' => 300000],
            ['nama' => 'Kabel', 'harga' => 20000],
            ['nama' => 'Sparepart', 'harga' => 100000],
            ['nama' => 'Kapasitor', 'harga' => 50000],
            ['nama' => 'Bracket AC', 'harga' => 75000],
            ['nama' => 'Material Umum', 'harga' => 15000],
        ];

        foreach ($kategori as $k) {
            KategoriKomponen::updateOrCreate(
                ['nama' => $k['nama']],
                ['harga' => $k['harga']]
            );
        }
    }
}
