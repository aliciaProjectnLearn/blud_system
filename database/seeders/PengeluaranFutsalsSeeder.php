<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengeluaranFutsalsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nominal' => 500000,  'deskripsi' => 'Pembelian bola futsal', 'tgl_pengeluaran' => '2026-01-05'],
            ['nominal' => 1200000, 'deskripsi' => 'Perawatan lapangan rumput sintetis', 'tgl_pengeluaran' => '2026-01-15'],
            ['nominal' => 300000,  'deskripsi' => 'Pembelian rompi latihan', 'tgl_pengeluaran' => '2026-02-03'],
            ['nominal' => 750000,  'deskripsi' => 'Perbaikan ring gawang', 'tgl_pengeluaran' => '2026-02-20'],
            ['nominal' => 450000,  'deskripsi' => 'Pembelian pompa angin dan alat tulis', 'tgl_pengeluaran' => '2026-03-08'],
            ['nominal' => 2000000, 'deskripsi' => 'Penggantian lampu lapangan', 'tgl_pengeluaran' => '2026-03-25'],
            ['nominal' => 600000,  'deskripsi' => 'Biaya kebersihan bulanan', 'tgl_pengeluaran' => '2026-04-01'],
            ['nominal' => 350000,  'deskripsi' => 'Pembelian perlengkapan P3K', 'tgl_pengeluaran' => '2026-04-05'],
        ];

        $no = 1;
        foreach ($data as $item) {
            DB::table('pengeluaran_futsals')->insert([
                'kode_pengeluaran' => 'PGF-' . str_pad($no++, 4, '0', STR_PAD_LEFT),
                'tgl_pengeluaran' => $item['tgl_pengeluaran'],
                'nominal' => $item['nominal'],
                'deskripsi' => $item['deskripsi'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
