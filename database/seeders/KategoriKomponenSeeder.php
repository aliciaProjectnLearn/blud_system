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
            ['nama' => 'Pipa AC'],
            ['nama' => 'Freon'],
            ['nama' => 'Kabel'],
            ['nama' => 'Sparepart'],
            ['nama' => 'Kapasitor'],
            ['nama' => 'Bracket AC'],
            ['nama' => 'Material Umum'],
        ];

        foreach ($kategori as $k) {
            KategoriKomponen::updateOrCreate(
                ['nama' => $k['nama']]
            );
        }
    }
}
