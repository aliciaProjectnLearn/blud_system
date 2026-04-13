<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengeluaranAcSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nominal' => 350000,  'deskripsi' => 'Pembelian freon R32', 'tanggal' => '2026-01-06'],
            ['nominal' => 750000,  'deskripsi' => 'Pembelian sparepart kompresor', 'tanggal' => '2026-01-18'],
            ['nominal' => 200000,  'deskripsi' => 'Pembelian alat cuci AC (kuas & selang)', 'tanggal' => '2026-02-05'],
            ['nominal' => 500000,  'deskripsi' => 'Pembelian freon R410A', 'tanggal' => '2026-02-22'],
            ['nominal' => 1200000, 'deskripsi' => 'Pembelian tangki nitrogen', 'tanggal' => '2026-03-10'],
            ['nominal' => 400000,  'deskripsi' => 'Servis alat ukur manifold gauge', 'tanggal' => '2026-03-20'],
            ['nominal' => 300000,  'deskripsi' => 'Pembelian filter udara', 'tanggal' => '2026-04-01'],
            ['nominal' => 650000,  'deskripsi' => 'Pembelian perlengkapan teknisi baru', 'tanggal' => '2026-04-07'],
        ];

        foreach ($data as $item) {
            DB::table('pengeluaran_ac')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
