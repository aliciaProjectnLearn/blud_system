<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
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
        // Ambil testimoni: 5 approved
        $testimoni = Testimonial::with('user')
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        $layanans = \App\Models\Layanan::all();

        return view('user.gateway', compact('testimoni', 'layanans'));
    }
}
