<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('membership')->insert([
            // data dummy telah dihapus.
        ]);
    }
}
