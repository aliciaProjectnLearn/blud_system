<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembayaranAcTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pembayaran_ac')->insert([
            [
                'booking_id'          => 1, // dari booking_ac id 1
                'layanan_ac_id'       => 1,
                'tgl_servis'          => now()->toDateString(),
                'total_harga_jasa'    => 50000,
                'total_biaya'         => 100000,
                'tgl_bayar'           => now(),
                'tipe_pembayaran_id'  => 1,
                'status'              => 'verifikasi',
                'bukti'               => 'bukti_ac1.jpg',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'booking_id'          => 2, // dari booking_ac id 2
                'layanan_ac_id'       => 2,
                'tgl_servis'          => now()->addDay()->toDateString(),
                'total_harga_jasa'    => 75000,
                'total_biaya'         => 150000,
                'tgl_bayar'           => null,
                'tipe_pembayaran_id'  => 2,
                'status'              => 'menunggu',
                'bukti'               => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'booking_id'          => 3, // dari booking_ac id 3
                'layanan_ac_id'       => 1,
                'tgl_servis'          => now()->subDay()->toDateString(),
                'total_harga_jasa'    => 50000,
                'total_biaya'         => 100000,
                'tgl_bayar'           => now()->subDay(),
                'tipe_pembayaran_id'  => 1,
                'status'              => 'verifikasi', // diubah dari 'dibatalkan'
                'bukti'               => 'bukti_ac3.jpg',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}