<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailServis;

class DetailServisTableSeeder extends Seeder
{
    public function run()
    {
        $details = [
            [
                'booking_id' => 1,
                'item'       => 'Cuci AC 0.5 - 1 PK',
                'satuan'     => 'Unit',
                'quantity'   => 2,
                'harga'      => 75000,
                'catatan'    => 'Kondisi awal sangat kotor',
            ],
            [
                'booking_id' => 1,
                'item'       => 'Isi Freon R32',
                'satuan'     => 'Psi',
                'quantity'   => 1,
                'harga'      => 150000,
                'catatan'    => 'Pengisian full',
            ],
            [
                'booking_id' => 2,
                'item'       => 'Perbaikan Modul',
                'satuan'     => 'Set',
                'quantity'   => 1,
                'harga'      => 350000,
                'catatan'    => 'Ganti kapasitor',
            ],
            [
                'booking_id' => 3,
                'item'       => 'Cuci AC 1.5 - 2 PK',
                'satuan'     => 'Unit',
                'quantity'   => 1,
                'harga'      => 100000,
                'catatan'    => 'Servis rutin',
            ],
        ];

        foreach ($details as $data) {
            DetailServis::create($data); // Menggunakan model agar subtotal terhitung di boot method
        }
    }
}