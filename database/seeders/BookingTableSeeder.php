<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('booking')->insert([
            // User ID 5 (Pelanggan 1)
            [
                'id'         => 1,
                'user_id'    => 5,
                'status'     => 'selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'user_id'    => 5,
                'status'     => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 3,
                'user_id'    => 5,
                'status'     => 'dibatalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User ID 6 (Pelanggan 2) - Data Induk AC
            [
                'id'         => 4,
                'user_id'    => 6,
                'status'     => 'dikonfirmasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 5,
                'user_id'    => 6,
                'status'     => 'dikonfirmasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 6,
                'user_id'    => 6,
                'status'     => 'dikonfirmasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User ID 7 (Pelanggan 3)
            [
                'id'         => 7,
                'user_id'    => 7,
                'status'     => 'selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 8,
                'user_id'    => 7,
                'status'     => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 9,
                'user_id'    => 7,
                'status'     => 'dibatalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}