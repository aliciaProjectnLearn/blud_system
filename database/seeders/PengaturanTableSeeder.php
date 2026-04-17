<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengaturanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Pengaturan::create([
            'jam_buka'             => '07:00:00',
            'jam_tutup'            => '22:00:00',
            'harga_reguler_futsal' => 75000,
            'harga_event_futsal'   => 800000,
        ]);
    }
}
