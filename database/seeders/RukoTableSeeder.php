<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RukoTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ruko')->insert([
            [
                'kode_unit'   => 'UNT001',
                'kategori_id' => 1,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_unit'   => 'UNT002',
                'kategori_id' => 1,
                'harga'       => 9000000,
                'status_unit' => 'terisi',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_unit'   => 'UNT003',
                'kategori_id' => 2,
                'harga'       => 6000000,
                'status_unit' => 'kosong',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}