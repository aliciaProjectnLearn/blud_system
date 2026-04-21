<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BookingServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    /**
     * Menampilkan daftar pelanggan servis.
     * Pelanggan didefinisikan sebagai user yang memiliki minimal 1 booking servis.
     */
    public function index(Request $request)
    {
        // Query dasar: User yang memiliki relasi ke booking_servis
        $query = User::whereHas('bookingServis');

        // Search: Nama, Email, No HP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        // Eager Loading & aggregasi
        $pelanggans = $query->withCount('bookingServis')
            ->withSum(['pembayaranServis as total_pengeluaran' => function($q) {
                $q->where('status_pembayaran', 'lunas');
            }], 'total_biaya')
            ->withMax('bookingServis as terakhir_booking', 'tanggal_booking')
            ->with(['bookingServis' => function($q) {
                $q->latest()->limit(1);
            }]);

        // Filter: Jumlah Booking
        if ($request->filled('min_booking')) {
            $pelanggans->having('booking_servis_count', '>=', $request->min_booking);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort == 'total_booking') {
            $pelanggans->orderBy('booking_servis_count', 'desc');
        } elseif ($sort == 'oldest') {
            $pelanggans->orderBy('terakhir_booking', 'asc');
        } else {
            // Default latest booking
            $pelanggans->orderBy('terakhir_booking', 'desc');
        }

        $pelanggans = $pelanggans->paginate(10)->withQueryString();

        // Statistik Ringkas (Optional Enhancement)
        $stats = [
            'total_pelanggan' => User::whereHas('bookingServis')->count(),
            'pelanggan_aktif' => User::whereHas('bookingServis', function($q) {
                $q->where('tanggal_booking', '>=', now()->subDays(30));
            })->count(),
            'total_transaksi' => BookingServis::count(),
        ];

        return view('adminservis.pelanggan.index', compact('pelanggans', 'stats'));
    }

    public function show($id)
    {
        // Ambil data pelanggan utama
        $pelanggan = User::whereHas('bookingServis')
            ->withCount('bookingServis')
            ->withSum(['pembayaranServis as total_pengeluaran' => function($q) {
                $q->where('status_pembayaran', 'lunas');
            }], 'total_biaya')
            ->findOrFail($id);

        // Ambil riwayat booking dengan pagination
        $history = $pelanggan->bookingServis()
            ->with(['layananServis', 'pembayaranServis'])
            ->latest()
            ->paginate(10);

        return view('adminservis.pelanggan.show', compact('pelanggan', 'history'));
    }
}
