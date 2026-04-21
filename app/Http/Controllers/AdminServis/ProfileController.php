<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = [
            'nama'         => 'Ahmad Fauzi, S.Kom',
            'nip'          => '198705142010011002',
            'email'        => 'ahmad.fauzi@bludservis.go.id',
            'telepon'      => '081234567890',
            'jabatan'      => 'Administrator Sistem',
            'unit'         => 'BLUD Servis Kendaraan Daerah',
            'tglBergabung' => '14 Januari 2022',
            'totalLogin'   => 342,
            'terakhirLogin'=> 'Hari ini, 08.45 WIB',
        ];

        $aktivitas = [
            ['waktu' => '21 Apr 2026, 08:45', 'aktivitas' => 'Login berhasil', 'ip_address' => '192.168.1.10'],
            ['waktu' => '21 Apr 2026, 09:20', 'aktivitas' => 'Melihat laporan transaksi', 'ip_address' => '192.168.1.10'],
            ['waktu' => '21 Apr 2026, 10:15', 'aktivitas' => 'Mengubah status servis #SRV-2026-0012', 'ip_address' => '192.168.1.10'],
            ['waktu' => '20 Apr 2026, 16:30', 'aktivitas' => 'Export data PDF', 'ip_address' => '192.168.1.15'],
            ['waktu' => '20 Apr 2026, 17:05', 'aktivitas' => 'Logout', 'ip_address' => '192.168.1.15'],
        ];

        return view('adminservis.profile', compact('admin', 'aktivitas'));
    }
}
