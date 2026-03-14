<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('membership')->insert([
            [
                'user_id'     => 5,
                'sisa_kuota'  => 10,
                'tgl_daftar'  => '2026-02-12',
                'status'      => 'aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'user_id'     => 3,
                'sisa_kuota'  => 5,
                'tgl_daftar'  => '2026-02-27',
                'status'      => 'aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'user_id'     => 1,
                'sisa_kuota'  => 0,
                'tgl_daftar'  => '2026-01-13',
                'status'      => 'tidak aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}