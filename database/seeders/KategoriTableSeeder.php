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
                'harga' => 9000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kantin Container',
                'harga' => 6000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Service AC',
                'harga' => 2000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
