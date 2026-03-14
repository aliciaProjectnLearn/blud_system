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
                'kategori_id' => 1,
                'status_unit' => 'kosong',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kategori_id' => 1,
                'status_unit' => 'terisi',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kategori_id' => 2,
                'status_unit' => 'kosong',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}