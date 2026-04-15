<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingFutsalTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('booking_futsal')->insert([
            // Pelanggan (id:5) - reguler, booking kemarin → ACTIVE
            [
                'booking_id'        => 1,
                'user_id'           => 5,
                'lapangan_id'       => 1,
                'start_datetime'    => Carbon::now()->subDays(1)->format('Y-m-d 15:00:00'),
                'end_datetime'      => Carbon::now()->subDays(1)->format('Y-m-d 16:00:00'),
                'type'              => 'regular',
                'jenis_pembayaran'  => 'reguler',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Pelanggan 2 (id:6) - membership (tidak muncul di pelanggan reguler)
            [
                'booking_id'        => 2,
                'user_id'           => 6,
                'lapangan_id'       => 1,
                'start_datetime'    => Carbon::now()->addDays(2)->format('Y-m-d 16:00:00'),
                'end_datetime'      => Carbon::now()->addDays(2)->format('Y-m-d 18:00:00'),
                'type'              => 'regular',
                'jenis_pembayaran'  => 'membership',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Pelanggan 3 (id:7) - membership (tidak muncul di pelanggan reguler)
            [
                'booking_id'        => 3,
                'user_id'           => 7,
                'lapangan_id'       => 1,
                'start_datetime'    => Carbon::now()->addDays(3)->format('Y-m-d 19:00:00'),
                'end_datetime'      => Carbon::now()->addDays(3)->format('Y-m-d 20:00:00'),
                'type'              => 'regular',
                'jenis_pembayaran'  => 'membership',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Pelanggan 4 (id:8) - reguler, booking 35 hari lalu → INACTIVE
            [
                'booking_id'        => null,
                'user_id'           => 8,
                'lapangan_id'       => 1,
                'start_datetime'    => Carbon::now()->subDays(35)->format('Y-m-d 13:00:00'),
                'end_datetime'      => Carbon::now()->subDays(35)->format('Y-m-d 14:00:00'),
                'type'              => 'regular',
                'jenis_pembayaran'  => 'reguler',
                'created_at'        => now()->subDays(35),
                'updated_at'        => now()->subDays(35),
            ],
        ]);
    }
}