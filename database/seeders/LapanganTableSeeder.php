<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LapanganTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lapangan')->insert([
            [
                'nama' => 'Lapangan Utama',
                'spesifikasi' => 'lapangan ini memiliki ukuran25x15',
                'deskripsi' => 'Lapangan Futsal dengan fasilitas lengkap',
                'foto' => 'lapangan_a.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // tambahkan data lain
        ]);
    }
}