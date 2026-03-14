<?php

namespace Database\Seeders;
// UsersTableSeeder.php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// TipePembayaranTableSeeder.php
class TipePembayaranTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipe_pembayaran')->insert([
            ['nama' => 'Transfer Bank', 'created_at' => now(),'updated_at' => now()],
            ['nama' => 'Tunai', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}