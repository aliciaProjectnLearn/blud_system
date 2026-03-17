<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogActivityTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('log_activities')->insert([
            [
                'user_id' => 1,
                'nama_user' => 'Super Admin',
                'sistem' => 'Auth',
                'aktivitas' => 'login',
                'deskripsi_aktivitas' => 'User login ke sistem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'nama_user' => 'Admin Futsal',
                'sistem' => 'Futsal',
                'aktivitas' => 'create',
                'deskripsi_aktivitas' => 'Menambahkan lapangan baru',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'user_id' => 3,
                'nama_user' => 'Admin AC',
                'sistem' => 'AC',
                'aktivitas' => 'update',
                'deskripsi_aktivitas' => 'Mengubah harga layanan AC',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);
    }
}