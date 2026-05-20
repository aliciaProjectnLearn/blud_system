<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class LandingController extends Controller
{
    public function home()
    {
        if (Auth::check()) {
            $roleName = strtolower(Auth::user()->roles->first()?->nama ?? '');

            return match($roleName) {
                'superadmin'  => redirect()->route('admin.dashboard'),
                'adminfutsal' => redirect()->route('admin.futsal.dashboard'),
                'adminkantin' => redirect()->route('admin.kantin.dashboard'),
                'adminac'     => redirect()->route('admin.ac.dashboard'),
                'adminservis' => redirect()->route('admin.servis.dashboard'),
                'teknisi'     => redirect()->route('teknisi.dashboard'),
                'kasir'       => redirect()->route('kasir.dashboard'),
                default       => redirect()->route('user.gateway'),
            };
        }

        return redirect()->route('user.gateway');
    }

    public function index()
    {
        // Ambil testimoni yang ditampilkan (max 6)
        $testimoni = Testimoni::tampil()->latest()->take(6)->get();

        return view('user.gateway', compact('testimoni'));
    }
}
