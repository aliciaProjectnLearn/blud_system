<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PembayaranFutsal;

class PembayaranFutsalTableSeeder extends Seeder
{
    public function run()
    {
        $data = [

        ];

        foreach ($data as $item) {
            PembayaranFutsal::create($item);
        }
    }
}