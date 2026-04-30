<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\PengeluaranKantin;
use Carbon\Carbon;

class PengeluaranKantinSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'deskripsi' => 'Perbaikan atap kantin blok A',
                'nominal' => 1500000,
                'kategori_pengeluaran' => 'pemeliharaan',
                'tanggal' => Carbon::now()->subDays(5),
            ],
            [
                'deskripsi' => 'Pembelian lampu dan instalasi listrik',
                'nominal' => 750000,
                'kategori_pengeluaran' => 'pemeliharaan',
                'tanggal' => Carbon::now()->subDays(12),
            ],
            [
                'deskripsi' => 'Biaya kebersihan bulanan',
                'nominal' => 500000,
                'kategori_pengeluaran' => 'operasional',
                'tanggal' => Carbon::now()->subDays(20),
            ],
            [
                'deskripsi' => 'Pengecatan ulang unit ruko B',
                'nominal' => 2000000,
                'kategori_pengeluaran' => 'pemeliharaan',
                'tanggal' => Carbon::now()->subMonth(),
            ],
            [
                'deskripsi' => 'Perbaikan saluran air',
                'nominal' => 850000,
                'kategori_pengeluaran' => 'pemeliharaan',
                'tanggal' => Carbon::now()->subMonth()->subDays(5),
            ],
            [
                'deskripsi' => 'Pembelian alat kebersihan',
                'nominal' => 300000,
                'kategori_pengeluaran' => 'operasional',
                'tanggal' => Carbon::now()->subMonths(2),
            ],
        ];

        foreach ($data as $item) {
            PengeluaranKantin::create($item);
        }
    }
}
