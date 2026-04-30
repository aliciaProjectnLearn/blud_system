<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PembayaranAc;

class PembayaranAcTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pembayaran_ac')->insert([
            [
                'booking_id'          => 1,
                'invoice_no'          => 'INV-20260404-0001',
                'total_harga'         => 300000,
                'tgl_bayar'           => now(),
                'tipe_pembayaran_id'  => 1,
                'status'              => 'dibayar',
                'bukti'               => 'bukti_ac1.jpg',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'booking_id'          => 2,
                'invoice_no'          => 'INV-20260404-0002',
                'total_harga'         => 350000,
                'tgl_bayar'           => null,
                'tipe_pembayaran_id'  => 2,
                'status'              => 'pending',
                'bukti'               => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'booking_id'          => 3,
                'invoice_no'          => 'INV-20260404-0003',
                'total_harga'         => 100000,
                'tgl_bayar'           => now(),
                'tipe_pembayaran_id'  => 1,
                'status'              => 'dibayar',
                'bukti'               => 'bukti_ac3.jpg',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}