<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\BookingFutsal;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        // Auto set inactive saat halaman dibuka
        $this->updateStatusPelanggan();

        // Ambil user yang punya riwayat booking reguler
        $query = User::whereHas('bookingFutsal', function ($q) {
            $q->where('jenis_pembayaran', 'reguler');
        })->with(['bookingFutsal' => function ($q) {
            $q->where('jenis_pembayaran', 'reguler')->latest('start_datetime');
        }]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status_futsal', $request->status);
        }

        $pelanggans = $query->latest()->paginate(15);

        $stats = [
            'total'    => User::whereHas('bookingFutsal', fn($q) => $q->where('jenis_pembayaran', 'reguler'))->count(),
            'active'   => User::whereHas('bookingFutsal', fn($q) => $q->where('jenis_pembayaran', 'reguler'))->where('status_futsal', 'active')->count(),
            'inactive' => User::whereHas('bookingFutsal', fn($q) => $q->where('jenis_pembayaran', 'reguler'))->where('status_futsal', 'inactive')->count(),
        ];

        return view('adminfutsal.pelanggan.index', compact('pelanggans', 'stats'));
    }

    // Tambahkan method ini di bawahnya
    private function updateStatusPelanggan(): void
    {
        $userIds = Booking::whereHas('bookingFutsal', function ($q) {
                $q->where('jenis_pembayaran', 'reguler');
            })
            ->pluck('user_id')
            ->unique();

        foreach ($userIds as $userId) {
            $lastBooking = BookingFutsal::whereHas('booking', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->where('jenis_pembayaran', 'reguler')
                ->latest('start_datetime')
                ->first();

            $status = ($lastBooking && now()->diffInDays($lastBooking->start_datetime, true) > 30)
                ? 'inactive'
                : 'active';

            User::where('id', $userId)->update(['status_futsal' => $status]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8',
            'no_hp'        => 'nullable|string|max:20',
            'status_futsal' => 'required|in:active,inactive',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'no_hp'         => $request->no_hp,
            'status_futsal' => $request->status_futsal,
        ]);

        return redirect()->route('adminfutsal.pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'no_hp'         => 'nullable|string|max:20',
            'status_futsal' => 'required|in:active,inactive',
        ]);

        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'no_hp'         => $request->no_hp,
            'status_futsal' => $request->status_futsal,
        ]);

        return redirect()->route('adminfutsal.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diupdate.');
    }
}
