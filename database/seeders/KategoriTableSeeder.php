<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori')->insert([
            [
                'nama' => 'Kantin Besar',
                'tipe' => 'kantin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kantin Container',
                'tipe' => 'kantin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Service AC',
                'tipe' => 'ac',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
