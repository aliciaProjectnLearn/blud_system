<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LayananServis;
use App\Models\BookingServis;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserServisController extends Controller
{
    /**
     * Tampilkan Katalog Layanan.
     */
    public function katalog()
    {
        $layanans = LayananServis::where('is_active', true)->get();
        return view('user.servis.katalog', compact('layanans'));
    }

    /**
     * Tampilkan Form Booking.
     */
    public function booking(Request $request)
    {
        if (!$request->has('layanan_id')) {
            return redirect()->route('user.servis.katalog')->with('error', 'Silakan pilih layanan dari katalog terlebih dahulu.');
        }

        $layananTerpilih = LayananServis::where('is_active', true)->findOrFail($request->layanan_id);

        return view('user.servis.booking', compact('layananTerpilih'));
    }

    /**
     * Simpan booking servis baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'no_hp'             => 'required|string|max:20',
            'layanan_servis_id' => 'required|exists:layanan_servis,id',
            'tipe_kendaraan'    => 'required|in:motor,mobil',
            'merek_kendaraan'   => 'required|string|max:100',
            'nomor_plat'        => 'required|string|max:20',
            'tahun_kendaraan'   => 'nullable|digits:4|integer|min:1990|max:' . date('Y'),
            'keluhan'           => 'nullable|string|max:500',
            'tanggal_booking'   => 'required|date|after_or_equal:today',
            'jam_booking'       => 'required|in:' . implode(',', $this->generateJamSlot()),
        ]);

        $waktuBooking = Carbon::parse($request->tanggal_booking . ' ' . $request->jam_booking);
        if ($waktuBooking->isPast()) {
            return back()->withErrors(['jam_booking' => 'Waktu yang dipilih sudah lewat.'])->withInput();
        }

        if (!BookingServis::isSlotAvailable($request->tanggal_booking, $request->jam_booking)) {
            return back()->withErrors(['jam_booking' => 'Slot pada jam ini sudah penuh (Maks. 3).'])->withInput();
        }

        // Find or create user
        $user = User::where('no_hp', $request->no_hp)->first();
        if (!$user) {
            $user = User::create([
                'name' => explode(' ', $request->nama)[0],
                'nama_lengkap' => $request->nama,
                'no_hp' => $request->no_hp,
                'role' => 'pelanggan',
                'password' => bcrypt(Str::random(16)),
            ]);
            
            $role = Role::where('nama', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        $accessToken = bin2hex(random_bytes(32));

        $booking = BookingServis::create([
            'user_id'           => $user->id,
            'layanan_servis_id' => $request->layanan_servis_id,
            'tipe_kendaraan'    => $request->tipe_kendaraan,
            'merek_kendaraan'   => $request->merek_kendaraan,
            'nomor_plat'        => strtoupper($request->nomor_plat),
            'tahun_kendaraan'   => $request->tahun_kendaraan,
            'keluhan'           => $request->keluhan,
            'tanggal_booking'   => $request->tanggal_booking,
            'jam_booking'       => $request->jam_booking,
            'status'            => 'menunggu',
            'access_token'      => $accessToken,
        ]);

        return redirect()->route('user.token.show', $accessToken)
            ->with('success', 'Booking berhasil dikirim!');
    }

    /**
     * Endpoint AJAX untuk cek ketersediaan slot jam per tanggal.
     */
    public function getSlot(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
        ]);

        $tanggal = $request->tanggal;
        $jamTersedia = $this->generateJamSlot();

        $bookingPerJam = BookingServis::where('tanggal_booking', $tanggal)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->selectRaw('jam_booking, COUNT(*) as total')
            ->groupBy('jam_booking')
            ->pluck('total', 'jam_booking')
            ->toArray();

        $slots = [];
        foreach ($jamTersedia as $jam) {
            $total = $bookingPerJam[$jam] ?? 0;
            $slots[] = [
                'jam'       => $jam,
                'terisi'    => (int)$total,
                'kapasitas' => 3,
                'tersedia'  => $total < 3,
            ];
        }

        return response()->json($slots);
    }

    private function generateJamSlot(): array
    {
        $slots = [];
        for ($jam = 8; $jam <= 16; $jam++) {
            $slots[] = sprintf('%02d:00', $jam);
        }
        return $slots;
    }
}
