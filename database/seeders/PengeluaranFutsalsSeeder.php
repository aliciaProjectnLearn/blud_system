<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengeluaranFutsalsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            //data dummy telah di hapus.
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
