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
                'user_id'     => 6,
                'paket_membership_id' => 1,
                'total_kuota' => 5,
                'sisa_kuota'  => 4,
                'tgl_daftar'  => '2026-02-12',
                'status'      => 'aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'user_id'     => 7,
                'paket_membership_id' => 2,
                'total_kuota' => 10,
                'sisa_kuota'  => 5,
                'tgl_daftar'  => '2026-02-27',
                'status'      => 'aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
