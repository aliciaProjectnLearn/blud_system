<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingFutsalTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('booking_futsal')->insert([
            // data dummy telah dihapus.
        ]);
    }
}