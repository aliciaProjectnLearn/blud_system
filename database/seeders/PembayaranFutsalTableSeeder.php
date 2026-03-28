<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PembayaranFutsal;

class PembayaranFutsalTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'booking_id'          => 1,
                'tipe_pembayaran_id'  => 1,
                'jumlah_bayar'        => 100000,
                'status'              => 'verifikasi',
                'bukti'               => 'bukti1.jpg',
                'tgl_bayar'           => now(),
            ],
            [
                'booking_id'          => 2,
                'tipe_pembayaran_id'  => 2,
                'jumlah_bayar'        => 150000,
                'status'              => 'menunggu',
                'bukti'               => null,
                'tgl_bayar'           => null,
            ],
            [
                'booking_id'          => 3,
                'tipe_pembayaran_id'  => 1,
                'jumlah_bayar'        => 200000,
                'status'              => 'dibatalkan',
                'bukti'               => 'bukti3.jpg',
                'tgl_bayar'           => now()->subDays(2),
            ],
        ];

        foreach ($data as $item) {
            PembayaranFutsal::create($item);
        }
    }
}