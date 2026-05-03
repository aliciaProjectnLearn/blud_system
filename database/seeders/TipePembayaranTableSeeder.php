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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tipe_pembayaran')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        DB::table('tipe_pembayaran')->insert([
            ['id' => 1, 'nama' => 'Transfer Bank', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama' => 'Tunai',         'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama' => 'QRIS',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama' => 'Paket/Membership', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}