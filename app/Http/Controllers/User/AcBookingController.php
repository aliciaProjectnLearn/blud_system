<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAc;
use App\Models\LayananAc;
use App\Models\User;
use App\Models\Role;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcBookingController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->status;
        $layanans = LayananAc::all();
        
        // Query Dasar
        $query = BookingAc::with(['layanan', 'booking', 'pembayaran']);
        
        // Filter Berdasarkan Auth (Jika login) atau Kosongkan (Jika guest)
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            // Pelanggan tanpa login tidak melihat riwayat apapun di index umum ini.
            // Riwayat mereka diakses via TokenAccessController.
            $query->whereRaw('1 = 0'); 
        }
        
        // Filter Status
        if ($statusFilter && in_array($statusFilter, ['menunggu', 'proses', 'selesai', 'canceled'])) {
            if ($statusFilter === 'canceled') {
                $query->whereHas('booking', function($q) {
                    $q->where('status', 'dibatalkan');
                });
            } else {
                $query->where('status', $statusFilter);
            }
        }
        
        $bookings = $query->latest()->paginate(10);
        
        // Hitung Stats
        $statsQuery = BookingAc::query();
        if (auth()->check()) {
            $statsQuery->where('user_id', auth()->id());
        } else {
            $statsQuery->whereRaw('1 = 0');
        }
        
        $stats = [
            'total'   => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'menunggu')->count(),
            'proses'  => (clone $statsQuery)->where('status', 'proses')->count(),
            'selesai' => (clone $statsQuery)->where('status', 'selesai')->count(),
        ];

        return view('user.ac.index', compact('layanans', 'bookings', 'stats', 'statusFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'no_hp'           => 'required|string|max:20',
            'layanan_id'      => 'required|exists:layanan_ac,id',
            'tgl_kunjungan'   => 'required|date|after_or_equal:today',
            'alamat'          => 'required|string|max:255',
            'merek_ac'        => 'nullable|string|max:100',
            'detail_keluhan'  => 'nullable|string',
        ]);

        $user = User::where('no_hp', $validated['no_hp'])->first();
        if (!$user) {
            $user = User::create([
                'name' => explode(' ', $validated['nama'])[0],
                'nama_lengkap' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'role' => 'pelanggan',
                'password' => bcrypt(Str::random(16)),
            ]);
            
            $role = Role::where('nama', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        $accessToken = bin2hex(random_bytes(32));

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id' => $user->id,
                'status'  => 'menunggu',
                'access_token' => $accessToken,
            ]);

            BookingAc::create([
                'booking_id'      => $booking->id,
                'user_id'         => $user->id,
                'layanan_id'      => $validated['layanan_id'],
                'tgl_kunjungan'   => $validated['tgl_kunjungan'],
                'alamat'          => $validated['alamat'],
                'merek_ac'        => $validated['merek_ac'],
                'detail_keluhan'  => $validated['detail_keluhan'],
                'status'          => 'menunggu',
                'access_token'    => $accessToken,
            ]);

            DB::commit();
            return redirect()->route('user.token.show', $accessToken)->with('success', 'Booking AC berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function layanan()
    {
        $layanans = LayananAc::with('kategori')->paginate(12);
        
        // Ambil semua kategori yang terkait dengan Layanan AC
        $kategoris = Kategori::whereHas('layananAc')->get();

        return view('user.ac.layanan', compact('layanans', 'kategoris'));
    }
}
