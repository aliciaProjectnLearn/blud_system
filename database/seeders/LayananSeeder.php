<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanans = [
            [
                'nama_layanan' => 'Pusat Olahraga Futsal',
                'deskripsi' => 'Booking lapangan online, cek ketersediaan jadwal, dan kelola membership dengan mudah.',
                'icon_svg' => '<path d="M12 2v20M2 12h20m-6-6-12 12M18 6 6 18"/><circle cx="12" cy="12" r="10"/>',
                'route_name' => 'user.futsal.landing',
                'url' => null,
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'nama_layanan' => 'Penyewaan Kantin & Ruko',
                'deskripsi' => 'Informasi unit tersedia, pengajuan sewa, dan manajemen dokumen kontrak terpadu.',
                'icon_svg' => '<path d="M3 21h18M4 21V7l8-4v18M12 3v18M12 7h8v14M8 11h.01M8 15h.01M16 11h.01M16 15h.01"/>',
                'route_name' => 'user.kantin.katalog',
                'url' => null,
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'nama_layanan' => 'Layanan Servis AC',
                'deskripsi' => 'Pesan jasa perbaikan AC profesional dari teknisi bersertifikat.',
                'icon_svg' => '<path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2M9.6 4.6A2 2 0 1 1 11 8H2M12.6 19.4A2 2 0 1 0 14 16H2"/>',
                'route_name' => 'user.ac.layanan',
                'url' => null,
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'nama_layanan' => 'Servis Motor & Mobil',
                'deskripsi' => 'Layanan servis kendaraan berkala dengan peralatan modern dan mekanik ahli.',
                'icon_svg' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
                'route_name' => 'user.servis.katalog',
                'url' => null,
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'nama_layanan' => 'Pembuatan Aplikasi Web & Mobile',
                'deskripsi' => 'Layanan pengembangan aplikasi berbasis web & mobile sesuai kebutuhan Anda',
                'icon_svg' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6M12 4v16"/>',
                'route_name' => null,
                'url' => '#',
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($layanans as $layanan) {
            \App\Models\Layanan::create($layanan);
        }
    }
}
