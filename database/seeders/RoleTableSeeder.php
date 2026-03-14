<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nama' => 'Superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Adminfutsal', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Adminkantin', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Adminac', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pelanggan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}