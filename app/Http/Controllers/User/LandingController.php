<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function index()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            $roleName = strtolower(Auth::user()->roles->first()->nama ?? '');

            return match($roleName) {
                'superadmin'  => redirect()->route('dashboard'),
                'adminfutsal' => redirect()->route('adminfutsal.dashboard'),
                'adminkantin' => redirect()->route('adminkantin.dashboard'),
                'adminac'     => redirect()->route('adminac.dashboard'),
                'adminservis' => redirect()->route('adminservis.dashboard'),
                'teknisi'     => redirect()->route('teknisi.dashboard'),
                default       => redirect()->route('user.dashboard'),
            };
        }

        // Ambil testimoni yang ditampilkan (max 6)
        $testimoni = Testimoni::tampil()->latest()->take(6)->get();

        return view('user.gateway', compact('testimoni'));
    }
}
