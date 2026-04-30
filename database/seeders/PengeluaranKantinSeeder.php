<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengeluaranKantinSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nominal' => 800000,  'deskripsi' => 'Perbaikan saluran air kantin', 'tanggal' => '2026-01-08'],
            ['nominal' => 450000,  'deskripsi' => 'Pembelian peralatan kebersihan', 'tanggal' => '2026-01-20'],
            ['nominal' => 1500000, 'deskripsi' => 'Pengecatan ulang unit kantin blok A', 'tanggal' => '2026-02-10'],
            ['nominal' => 300000,  'deskripsi' => 'Penggantian kunci pintu unit 03', 'tanggal' => '2026-02-18'],
            ['nominal' => 600000,  'deskripsi' => 'Biaya administrasi sewa bulanan', 'tanggal' => '2026-03-01'],
            ['nominal' => 950000,  'deskripsi' => 'Perbaikan atap bocor unit kantin blok B', 'tanggal' => '2026-03-14'],
            ['nominal' => 250000,  'deskripsi' => 'Pembelian alat tulis kantor', 'tanggal' => '2026-04-02'],
            ['nominal' => 1200000, 'deskripsi' => 'Perbaikan instalasi listrik blok C', 'tanggal' => '2026-04-08'],
        ];

        foreach ($data as $item) {
            DB::table('pengeluaran_kantin')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
