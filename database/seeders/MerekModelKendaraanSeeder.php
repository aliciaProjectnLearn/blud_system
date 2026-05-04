<?php

namespace Database\Seeders;

use App\Models\MerekKendaraan;
use App\Models\ModelKendaraan;
use Illuminate\Database\Seeder;

class MerekModelKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Motor Brands & Models
        $motorBrands = [
            'Honda' => ['Beat', 'Vario 125', 'Vario 150', 'PCX', 'CBR 150R', 'Scoopy'],
            'Yamaha' => ['NMAX', 'Aerox', 'Lexi', 'Mio M3', 'R15', 'Fazzio'],
            'Suzuki' => ['Satria FU', 'NEX II', 'GSX-R150', 'Address'],
            'Kawasaki' => ['Ninja 250', 'KLX 150', 'W175', 'D-Tracker'],
        ];

        foreach ($motorBrands as $brand => $models) {
            $m = MerekKendaraan::create([
                'nama'      => $brand,
                'tipe'      => 'motor',
                'is_active' => true,
            ]);

            foreach ($models as $modelName) {
                ModelKendaraan::create([
                    'merek_kendaraan_id' => $m->id,
                    'nama_model'         => $modelName,
                    'is_active'          => true,
                ]);
            }
        }

        // 2. Mobil Brands & Models
        $mobilBrands = [
            'Toyota' => ['Avanza', 'Innova', 'Fortuner', 'Calya', 'Yaris', 'Agya'],
            'Daihatsu' => ['Xenia', 'Terios', 'Sigra', 'Ayla', 'Rocky'],
            'Honda' => ['Brio', 'HR-V', 'CR-V', 'Mobilio', 'Jazz', 'Civic'],
            'Suzuki' => ['Ertiga', 'XL7', 'Baleno', 'S-Presso', 'Ignis'],
            'Mitsubishi' => ['Xpander', 'Pajero Sport', 'Triton', 'Outlander'],
        ];

        foreach ($mobilBrands as $brand => $models) {
            $m = MerekKendaraan::create([
                'nama'      => $brand,
                'tipe'      => 'mobil',
                'is_active' => true,
            ]);

            foreach ($models as $modelName) {
                ModelKendaraan::create([
                    'merek_kendaraan_id' => $m->id,
                    'nama_model'         => $modelName,
                    'is_active'          => true,
                ]);
            }
        }
    }
}
