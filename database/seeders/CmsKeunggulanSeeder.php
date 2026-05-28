<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CmsKeunggulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'judul'     => 'Cepat & Sigap',
                'deskripsi' => 'Proses booking dan pengajuan yang efisien menghemat waktu Anda tanpa perlu antre panjang.',
                'icon_svg'  => 'M13 2 3 14h9l-1 8 10-12h-9l1-8z',
                'urutan'    => 1,
                'is_active' => true,
            ],
            [
                'judul'     => 'Transparan',
                'deskripsi' => 'Informasi harga, ketersediaan, dan status 100% jelas, terbuka, dan dapat dipantau.',
                'icon_svg'  => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10',
                'urutan'    => 2,
                'is_active' => true,
            ],
            [
                'judul'     => 'Terintegrasi',
                'deskripsi' => 'Satu pintu akses untuk semua kebutuhan penyewaan dan layanan jasa Anda.',
                'icon_svg'  => 'M12 2v20M2 12h20m-6-6-12 12M18 6 6 18',
                'urutan'    => 3,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            \App\Models\CmsKeunggulan::create($item);
        }
    }
}
