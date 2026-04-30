<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('roles')->insert([
            ['nama' => 'Superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Adminfutsal', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Adminkantin', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Adminac', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Adminservis', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pelanggan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Teknisi', 'created_at' => now(), 'updated_at' => now()], // Existing Teknisi untuk Service AC
            ['nama' => 'Teknisi Motor', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Teknisi Mobil', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
