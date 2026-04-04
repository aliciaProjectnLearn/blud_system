<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingAcTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('booking_ac')->insert([
            [
                'booking_id'      => 4,
                'user_id'         => 6,
                'teknisi_id'      => 9,
                'layanan_id'      => 1,
                'tgl_kunjungan'   => now()->toDateString(),
                'alamat'          => 'Jl. Merdeka No.1',
                'merek_ac'        => 'Panasonic',
                'detail_keluhan'  => 'AC tidak dingin',
                'status'          => 'selesai',
            ],
            [
                'booking_id'      => 5,
                'user_id'         => 6,
                'teknisi_id'      => 10,
                'layanan_id'      => 2,
                'tgl_kunjungan'   => now()->addDays(2)->toDateString(),
                'alamat'          => 'Jl. Merdeka No.2',
                'merek_ac'        => 'Daikin',
                'detail_keluhan'  => 'Bocor',
                'status'          => 'proses',
            ],
            [
                'booking_id'      => 6,
                'user_id'         => 6,
                'teknisi_id'      => null,
                'layanan_id'      => 3,
                'tgl_kunjungan'   => now()->addDays(3)->toDateString(),
                'alamat'          => 'Jl. Merdeka No.3',
                'merek_ac'        => 'LG',
                'detail_keluhan'  => 'Bunyi berisik',
                'status'          => 'menunggu',
            ],
        ]);
    }
}
