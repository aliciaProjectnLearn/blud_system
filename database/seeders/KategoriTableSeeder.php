<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriTableSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Kantin Besar',
                'tipe' => 'kantin',
            ],
            [
                'nama' => 'Kantin Container',
                'tipe' => 'kantin',
            ],
            [
                'nama' => 'Ruko Depan',
                'tipe' => 'kantin',
            ],
            [
                'nama' => 'Service AC',
                'tipe' => 'ac',
            ],
        ];

        foreach ($kategoris as $k) {
            DB::table('kategori')->updateOrInsert(
                ['nama' => $k['nama']],
                array_merge($k, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
