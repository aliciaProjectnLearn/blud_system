<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('booking')->insert([
            // User ID 5 (Pelanggan)
            [
                'user_id'    => 5,
                'status'     => 'selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 5,
                'status'     => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 5,
                'status'     => 'dibatalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User ID 6 (Pelanggan 2)
            [
                'user_id'    => 6,
                'status'     => 'selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 6,
                'status'     => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 6,
                'status'     => 'dibatalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User ID 7 (Pelanggan 3)
            [
                'user_id'    => 7,
                'status'     => 'selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 7,
                'status'     => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 7,
                'status'     => 'dibatalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}