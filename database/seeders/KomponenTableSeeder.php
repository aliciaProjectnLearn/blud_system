<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomponenTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('komponen')->insert([
            [
                'nama'           => 'Freon R22',
                'jenis_komponen' => 'Freon',
                'harga'          => 50000,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'nama'           => 'Kapasitor',
                'jenis_komponen' => 'Elektrik',
                'harga'          => 25000,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'nama'           => 'Fan Motor',
                'jenis_komponen' => 'Mekanik',
                'harga'          => 150000,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}