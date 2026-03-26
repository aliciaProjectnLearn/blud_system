<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaketMembershipSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('paket_membership')->insert([
            [
                'nama_paket'   => 'Paket Starter',
                'jumlah_kuota' => 5,
                'harga'        => 150000,
                'status'       => 'aktif',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_paket'   => 'Paket Regular',
                'jumlah_kuota' => 10,
                'harga'        => 280000,
                'status'       => 'aktif',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_paket'   => 'Paket Premium',
                'jumlah_kuota' => 20,
                'harga'        => 500000,
                'status'       => 'aktif',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
