<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTransaksi = 1248;
        $totalPendapatan = 48750000;
        $servisHariIni = 12;
        $totalTeknisi = 8;

        $pendapatanBulanan = [
            3200000, 4100000, 3800000, 5200000, 4700000, 6100000,
            5500000, 4900000, 5800000, 6400000, 7200000, 5900000
        ];

        $transaksiTerbaru = [
            ['kode' => 'SRV-2026-0001', 'nama_pelanggan' => 'Andi Wijaya', 'jenis_kendaraan' => 'Mobil', 'jenis_servis' => 'Ganti Oli', 'teknisi' => 'Bambang', 'total_biaya' => 250000, 'status' => 'Selesai'],
            ['kode' => 'SRV-2026-0002', 'nama_pelanggan' => 'Siti Aminah', 'jenis_kendaraan' => 'Motor', 'jenis_servis' => 'Tune Up', 'teknisi' => 'Rizky', 'total_biaya' => 150000, 'status' => 'Proses'],
            ['kode' => 'SRV-2026-0003', 'nama_pelanggan' => 'Budi Santoso', 'jenis_kendaraan' => 'Mobil', 'jenis_servis' => 'Servis AC', 'teknisi' => 'Agus', 'total_biaya' => 450000, 'status' => 'Menunggu'],
            ['kode' => 'SRV-2026-0004', 'nama_pelanggan' => 'Dewi Lestari', 'jenis_kendaraan' => 'Motor', 'jenis_servis' => 'Ganti Ban', 'teknisi' => 'Dedi', 'total_biaya' => 200000, 'status' => 'Selesai'],
            ['kode' => 'SRV-2026-0005', 'nama_pelanggan' => 'Eko Prasetyo', 'jenis_kendaraan' => 'Mobil', 'jenis_servis' => 'Servis Rem', 'teknisi' => 'Bambang', 'total_biaya' => 350000, 'status' => 'Proses'],
            ['kode' => 'SRV-2026-0006', 'nama_pelanggan' => 'Farida Utama', 'jenis_kendaraan' => 'Motor', 'jenis_servis' => 'Ganti Oli', 'teknisi' => 'Rizky', 'total_biaya' => 80000, 'status' => 'Dibatalkan'],
            ['kode' => 'SRV-2026-0007', 'nama_pelanggan' => 'Gatot Kaca', 'jenis_kendaraan' => 'Mobil', 'jenis_servis' => 'Tune Up', 'teknisi' => 'Agus', 'total_biaya' => 500000, 'status' => 'Selesai'],
            ['kode' => 'SRV-2026-0008', 'nama_pelanggan' => 'Hendra Setiawan', 'jenis_kendaraan' => 'Motor', 'jenis_servis' => 'Servis Rem', 'teknisi' => 'Dedi', 'total_biaya' => 120000, 'status' => 'Menunggu'],
            ['kode' => 'SRV-2026-0009', 'nama_pelanggan' => 'Indah Permata', 'jenis_kendaraan' => 'Mobil', 'jenis_servis' => 'Ganti Oli', 'teknisi' => 'Bambang', 'total_biaya' => 300000, 'status' => 'Selesai'],
            ['kode' => 'SRV-2026-0010', 'nama_pelanggan' => 'Joko Widodo', 'jenis_kendaraan' => 'Motor', 'jenis_servis' => 'Tune Up', 'teknisi' => 'Rizky', 'total_biaya' => 100000, 'status' => 'Proses'],
        ];

        $jadwalHariIni = [
            ['jam' => '08:00', 'nama_pelanggan' => 'Andi Wijaya', 'no_polisi' => 'D 1234 ABC', 'jenis_kendaraan' => 'Mobil', 'keluhan' => 'Mesin kasar, minta ganti oli', 'teknisi' => 'Bambang', 'status' => 'Selesai'],
            ['jam' => '09:00', 'nama_pelanggan' => 'Siti Aminah', 'no_polisi' => 'Z 5678 EFG', 'jenis_kendaraan' => 'Motor', 'keluhan' => 'Tune up rutin', 'teknisi' => 'Rizky', 'status' => 'Sedang Dikerjakan'],
            ['jam' => '10:30', 'nama_pelanggan' => 'Budi Santoso', 'no_polisi' => 'T 9012 HIJ', 'jenis_kendaraan' => 'Mobil', 'keluhan' => 'AC tidak dingin', 'teknisi' => 'Agus', 'status' => 'Menunggu'],
            ['jam' => '11:00', 'nama_pelanggan' => 'Dewi Lestari', 'no_polisi' => 'D 3456 KLM', 'jenis_kendaraan' => 'Motor', 'keluhan' => 'Ganti ban belakang', 'teknisi' => 'Dedi', 'status' => 'Menunggu'],
            ['jam' => '13:00', 'nama_pelanggan' => 'Eko Prasetyo', 'no_polisi' => 'Z 7890 NOP', 'jenis_kendaraan' => 'Mobil', 'keluhan' => 'Rem bunyi mencit', 'teknisi' => 'Bambang', 'status' => 'Menunggu'],
            ['jam' => '14:30', 'nama_pelanggan' => 'Gatot Kaca', 'no_polisi' => 'T 1122 QRS', 'jenis_kendaraan' => 'Mobil', 'keluhan' => 'Servis berkala 10rb km', 'teknisi' => 'Agus', 'status' => 'Menunggu'],
            ['jam' => '15:30', 'nama_pelanggan' => 'Hendra Setiawan', 'no_polisi' => 'D 3344 TUV', 'jenis_kendaraan' => 'Motor', 'keluhan' => 'Lampu depan mati', 'teknisi' => 'Dedi', 'status' => 'Menunggu'],
        ];

        return view('adminservis.index', compact(
            'totalTransaksi',
            'totalPendapatan',
            'servisHariIni',
            'totalTeknisi',
            'pendapatanBulanan',
            'transaksiTerbaru',
            'jadwalHariIni'
        ));
    }
}
