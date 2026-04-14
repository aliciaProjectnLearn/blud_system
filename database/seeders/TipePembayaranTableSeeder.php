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
        $tipes = [
            ['nama' => 'Transfer Bank'],
            ['nama' => 'Tunai'],
            ['nama' => 'QRIS'],
        ];

        foreach ($tipes as $t) {
            DB::table('tipe_pembayaran')->updateOrInsert(
                ['nama' => $t['nama']],
                array_merge($t, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}