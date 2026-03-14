<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananAcTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('layanan_ac')->insert([
            [
                'kategori_id'   => 3,
                'nama'          => 'Servis AC 1 PK',
                'kapasitas_ac'  => '1 PK',
                'harga_jasa'    => 50000,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kategori_id'   => 3,
                'nama'          => 'Servis AC 2 PK',
                'kapasitas_ac'  => '2 PK',
                'harga_jasa'    => 75000,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kategori_id'   => 3,
                'nama'          => 'Servis AC 3 PK',
                'kapasitas_ac'  => '3 PK',
                'harga_jasa'    => 100000,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}