<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Tampilkan daftar pelanggan yang pernah melakukan booking AC.
     */
    public function index(Request $request)
    {
        // Query Dasar: Role Pelanggan yang memiliki minimal satu record di booking_ac
        $query = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Pelanggan');
        })->whereHas('bookingAc');

        // Fitur Search: Nama atau Email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // withCount untuk menghitung total pesanan per pelanggan
        $pelanggans = $query->withCount('bookingAc')
            ->paginate(10)
            ->withQueryString();

        return view('adminac.pelanggan.index', compact('pelanggans'));
    }

    /**
     * Tampilkan detail histori layanan per pelanggan.
     */
    public function show($id)
    {
        // Ambil user dengan histori booking AC
        $pelanggan = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Pelanggan');
        })
        ->with(['bookingAc' => function ($q) {
            $q->with('layanan')->latest();
        }])
        ->findOrFail($id);

        return view('adminac.pelanggan.show', compact('pelanggan'));
    }
}
