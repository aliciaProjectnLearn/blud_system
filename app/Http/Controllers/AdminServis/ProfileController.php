<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogActivity;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $admin = [
            'nama'         => $user->nama_lengkap ?? $user->name,
            'nip'          => $user->nik ?? '-',
            'email'        => $user->email,
            'telepon'      => $user->no_hp ?? '-',
            'jabatan'      => 'Administrator Sistem',
            'unit'         => 'BLUD Servis Kendaraan Daerah',
            'tglBergabung' => $user->created_at->translatedFormat('d F Y'),
            'totalLogin'   => '-',
            'terakhirLogin'=> '-',
        ];

        $aktivitas = LogActivity::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('adminservis.profile', compact('admin', 'aktivitas'));
    }
}
