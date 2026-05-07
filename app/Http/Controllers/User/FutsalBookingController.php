<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFutsal;
use App\Models\JadwalLapangan;
use App\Models\Lapangan;
use App\Models\Membership;
use App\Models\PaketMembership;
use App\Models\PembayaranFutsal;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FutsalBookingController extends Controller
{
    public function landing()
    {
        $lapangans = Lapangan::all();
        $paketMemberships = PaketMembership::where('status', 'aktif')->get();
        $membershipAktif = null; // Halaman publik

        return view('user.futsal.landing', compact('lapangans', 'paketMemberships', 'membershipAktif'));
    }

    public function paketForm()
    {
        $paketMemberships = PaketMembership::where('status', 'aktif')->get();
        $membershipAktif = null; // Jalur publik

        return view('user.futsal.paket', compact('paketMemberships', 'membershipAktif'));
    }

    public function checkMembership(Request $request)
    {
        $no_hp = $request->no_hp;
        if (!$no_hp) return response()->json(['success' => false, 'membership' => null]);
        
        $user = User::where('no_hp', $no_hp)->first();
        if (!$user) return response()->json(['success' => true, 'membership' => null]);

        $membership = Membership::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->with('paket')
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'membership' => $membership
        ]);
    }

    public function checkBookingAktif(Request $request)
    {
        $request->validate(['no_hp' => 'required|string']);

        $bookingAktif = BookingFutsal::where('no_hp', $request->no_hp)
            ->where('status', '!=', 'dibatalkan')
            ->where('end_datetime', '>=', now())
            ->first();

        if ($bookingAktif) {
            return response()->json([
                'aktif' => true,
                'pesan' => 'Nomor ini masih memiliki booking aktif ('
                    . $bookingAktif->status . ') sampai jam '
                    . Carbon::parse($bookingAktif->end_datetime)->format('H:i')
                    . '. Selesaikan atau batalkan dulu.',
            ]);
        }

        return response()->json(['aktif' => false]);
    }

    public function paketStore(Request $request)
    {
        $request->validate([
            'paket_membership_id' => 'required|exists:paket_membership,id',
            'nama_pemesan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'bukti_pembayaran' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $paket = PaketMembership::findOrFail($request->paket_membership_id);

        $user = User::where('no_hp', $request->no_hp)->first();
        if (!$user) {
            $user = User::create([
                'name'         => explode(' ', $request->nama_pemesan)[0],
                'username'     => $request->no_hp,
                'nama_lengkap' => $request->nama_pemesan,
                'email'        => $request->no_hp.'@gmail.com',
                'no_hp'        => $request->no_hp,
                'password'     => bcrypt(Str::random(16)),
            ]);
            $role = Role::where('nama', 'pelanggan')->first();
            if ($role) $user->roles()->attach($role->id);
        }

        DB::beginTransaction();
        try {
            // Simpan bukti pembayaran
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran_membership', 'public');
            }

            $pembayaran = PembayaranFutsal::create([
                'booking_id' => null, 
                'tipe_pembayaran_id' => 3, // QRIS
                'jumlah_bayar' => $paket->harga,
                'jenis_transaksi' => 'membership',
                'status' => 'menunggu', // Berubah dari 'verifikasi' ke 'menunggu'
                'bukti' => $buktiPath,
            ]);

            $membership = Membership::create([
                'user_id'             => $user->id,
                'paket_membership_id' => $paket->id,
                'status'              => 'menunggu', // Berubah dari 'aktif' ke 'menunggu'
                'sisa_kuota'          => $paket->jumlah_kuota,
                'total_kuota'         => $paket->jumlah_kuota,
                'tgl_daftar'          => now()->toDateString(),
                'transaksi_id'        => $pembayaran->id,
            ]);

            // Kirim Notifikasi WA
            $apiToken = env('FONNTE_TOKEN');
            $noHp = preg_replace('/[^0-9]/', '', $user->no_hp);
            $namaPemesan = $request->nama_pemesan;
            $namaPaket = $paket->nama_paket;
            $kuota = $paket->jumlah_kuota;
            $harga = number_format($paket->harga, 0, ',', '.');

            $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
            $pesan .= "Pembelian paket membership futsal Anda telah diterima. ✅\n\n";
            $pesan .= "📋 *Detail Pesanan:*\n";
            $pesan .= "📦 Paket   : *{$namaPaket}*\n";
            $pesan .= "⏱️ Kuota   : *{$kuota} Jam*\n";
            $pesan .= "💰 Harga   : *Rp {$harga}*\n";
            $pesan .= "📍 Status  : *Menunggu Verifikasi Admin*\n\n";
            $pesan .= "Pesanan Anda sedang dalam proses verifikasi pembayaran. Anda akan menerima notifikasi otomatis jika paket telah diaktifkan oleh admin.\n\n";
            $pesan .= "Terima kasih 🙏\n— Admin Futsal BLUD SMK";

            try {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target'      => $noHp,
                    'message'     => $pesan,
                    'countryCode' => '62',
                    'token'       => $apiToken
                ]);
            } catch (\Exception $e) {
                \Log::warning('Gagal kirim WA aktivasi membership: ' . $e->getMessage());
            }

            DB::commit();
            return redirect()->route('user.futsal.landing')
                ->with('success', 'Pembelian paket berhasil! Pesanan Anda sedang menunggu verifikasi admin. Kami akan mengaktifkan paket Anda segera.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $lapangans = Lapangan::all();
        $paketMemberships = PaketMembership::where('status', 'aktif')->get();
        
        $membership = null;
        $isFirstBooking = false;

        return view('user.futsal.booking', compact('lapangans', 'paketMemberships', 'membership', 'isFirstBooking'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangan,id',
            'tanggal'     => 'required|date|after_or_equal:today',
        ]);

        $hariIndo = \Carbon\Carbon::parse($request->tanggal)->locale('id')->isoFormat('dddd');
        $jamOperasional = \App\Models\JamOperasionalLapangan::where('lapangan_id', $request->lapangan_id)
            ->where('hari', ucfirst($hariIndo))
            ->where('is_aktif', true)
            ->first();

        // Default if not set in jam_operasional_lapangan
        $jamBuka = $jamOperasional->jam_buka ?? '08:00';
        $jamTutup = $jamOperasional->jam_tutup ?? '22:00';

        if ($jamOperasional) {
            $currentStart = Carbon::parse($jamBuka);
            $jamTutupLimit = Carbon::parse($jamTutup);
            
            while ($currentStart < $jamTutupLimit) {
                $jamMulai = $currentStart->format('H:i:s');
                $jamSelesai = $currentStart->copy()->addHour()->format('H:i:s');
                
                JadwalLapangan::firstOrCreate([
                    'tanggal' => $request->tanggal,
                    'lapangan_id' => $request->lapangan_id,
                    'jam_mulai' => $jamMulai,
                ], [
                    'jam_selesai' => $jamSelesai,
                    'status' => 'tersedia'
                ]);
                
                $currentStart->addHour();
            }
        }

        $jadwals = JadwalLapangan::where('lapangan_id', $request->lapangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('jam_mulai', '>=', $jamBuka)
            ->where('jam_selesai', '<=', $jamTutup)
            ->orderBy('jam_mulai')
            ->get(['id', 'jam_mulai', 'jam_selesai', 'status']);

        // Cek Jam Blokir (dari pengaturan admin, fleksibel - menggantikan hardcode jam sekolah)
        $pengaturanBlokir = \App\Models\Pengaturan::first();

        $slots = $jadwals->map(function ($slot) use ($request, $pengaturanBlokir, $hariIndo) {
            $startDatetime = Carbon::parse($request->tanggal . ' ' . $slot->jam_mulai);
            $endDatetime = Carbon::parse($request->tanggal . ' ' . $slot->jam_selesai);

            // 1. Check if it's already booked
            $isBooked = BookingFutsal::where('lapangan_id', $request->lapangan_id)
                ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['dibatalkan']))
                ->where(function($q) use ($startDatetime, $endDatetime) {
                    $q->where('start_datetime', '<', $endDatetime)
                    ->where('end_datetime', '>', $startDatetime);
                })
                ->exists();

            // 2. Cek Jam Blokir dari database (menggantikan hardcode jam sekolah)
            $isSchoolHour = false;
            if ($pengaturanBlokir && $pengaturanBlokir->jam_blokir_aktif) {
                $hariBlokir = explode(',', $pengaturanBlokir->hari_blokir ?? '');
                $isHariBlokir = in_array(ucfirst($hariIndo), $hariBlokir);
                if ($isHariBlokir) {
                    $jamBlokirMulai = $pengaturanBlokir->jam_blokir_mulai ?? '07:00:00';
                    $jamBlokirSelesai = $pengaturanBlokir->jam_blokir_selesai ?? '15:00:00';
                    if ($slot->jam_mulai >= $jamBlokirMulai && $slot->jam_mulai < $jamBlokirSelesai) {
                        $isSchoolHour = true;
                    }
                }
            }
                
            $booked = $isBooked || $slot->status === 'terisi' || $isSchoolHour;

            return [
                'id' => $slot->id,
                'jam_mulai' => \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i'),
                'jam_selesai' => \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i'),
                'jam_mulai_display' => \Carbon\Carbon::parse($slot->jam_mulai)->addMinutes(10)->format('H:i'),
                'jam_selesai_display' => \Carbon\Carbon::parse($slot->jam_selesai)->addMinutes(10)->format('H:i'),
                'booked' => $booked,
                'is_school_hour' => $isSchoolHour
            ];
        });

        return response()->json([
            'success'   => true,
            'slots'     => $slots,
            'jam_buka'  => $jamOperasional ? $jamOperasional->jam_buka : null,
            'jam_tutup' => $jamOperasional ? $jamOperasional->jam_tutup : null,
        ]);
    }

    public function store(Request $request)
    {
        $isEvent = $request->input('type') === 'event';

        // Mapping nama field dari view ke nama yang dipakai controller
        $request->merge([
            'tgl_main'  => $isEvent ? $request->input('start_datetime') : $request->input('tanggal'),
            'jam_mulai' => !$isEvent && $request->input('jam_mulai_id')
                            ? \App\Models\JadwalLapangan::find($request->input('jam_mulai_id'))?->jam_mulai
                            : ($isEvent ? '00:00:00' : null),
            'durasi'    => !$isEvent ? $request->input('durasi_main') : 1,
        ]);

        $rules = [
            'lapangan_id'  => 'required|exists:lapangan,id',
            'nama_pemesan' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
            'type'         => 'required|in:regular,event',
        ];

        if ($isEvent) {
            $rules['start_datetime'] = 'required|date|after_or_equal:today';
            $rules['end_datetime']   = 'required|date|after_or_equal:start_datetime';
        } else {
            $rules['tgl_main']  = 'required|date|after_or_equal:today';
            $rules['jam_mulai'] = 'required';
            $rules['durasi']    = 'required|integer|min:1|max:3';
        }

        $request->validate($rules);

        if ($isEvent) {
            $start = Carbon::parse($request->start_datetime)->startOfDay();
            $end   = Carbon::parse($request->end_datetime)->endOfDay();
        } else {
            $start = Carbon::parse($request->tgl_main . ' ' . $request->jam_mulai);
            $end   = $start->copy()->addHours((int) $request->durasi);
        }

        // Cek apakah no_hp sudah punya booking aktif (yang belum selesai)
        $bookingAktif = BookingFutsal::where('no_hp', $request->no_hp)
            ->where('status', '!=', 'dibatalkan')
            ->where('end_datetime', '>=', now())
            ->exists();

        if ($bookingAktif) {
            return back()->withInput()->with('error', 'Anda masih memiliki booking aktif. Selesaikan atau batalkan dulu sebelum membuat booking baru.');
        }

        // 3. Cek konflik jadwal di booking_futsal
        $conflict = BookingFutsal::where('lapangan_id', $request->lapangan_id)
            ->where('status', '!=', 'dibatalkan')
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_datetime', [$start, $end->copy()->subMinute()])
                  ->orWhereBetween('end_datetime', [$start->copy()->addMinute(), $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('start_datetime', '<=', $start)
                         ->where('end_datetime', '>=', $end);
                  });
            })->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'Jadwal sudah dipesan atau bertabrakan dengan event lain.');
        }

        if (!$isEvent) {
            // 4. Cek jam operasional (Hanya untuk reguler)
            $hariIndo = Carbon::parse($request->tgl_main)->locale('id')->isoFormat('dddd');
            $jamOps = \App\Models\JamOperasionalLapangan::where('lapangan_id', $request->lapangan_id)
                ->where('hari', ucfirst($hariIndo))
                ->where('is_aktif', true)
                ->first();

            if (!$jamOps) {
                return back()->withInput()->with('error', 'Di luar jam operasional (Tutup)');
            }

            $jamMulaiTime = $start->format('H:i:s');
            $jamSelesaiTime = $end->format('H:i:s');
            
            if ($jamMulaiTime < $jamOps->jam_buka || $jamSelesaiTime > $jamOps->jam_tutup) {
                return back()->withInput()->with('error', 'Di luar jam operasional');
            }

            // Cek Jam Blokir (dari pengaturan admin, fleksibel - menggantikan hardcode jam sekolah)
            $pengaturan = \App\Models\Pengaturan::first();
            if ($pengaturan && $pengaturan->jam_blokir_aktif) {
                $hariBlokir = explode(',', $pengaturan->hari_blokir ?? '');
                $isHariBlokir = in_array(ucfirst($hariIndo), $hariBlokir);

                if ($isHariBlokir) {
                    $jamBlokirMulai = $pengaturan->jam_blokir_mulai ?? '07:00:00';
                    $jamBlokirSelesai = $pengaturan->jam_blokir_selesai ?? '15:00:00';

                    // Booking diblokir jika jam mulai booking ada di dalam rentang blokir
                    if ($jamMulaiTime >= $jamBlokirMulai && $jamMulaiTime < $jamBlokirSelesai) {
                        $keterangan = $pengaturan->keterangan_blokir
                            ? " ({$pengaturan->keterangan_blokir})"
                            : '';
                        $jamMulaiTampil = \Carbon\Carbon::parse($jamBlokirMulai)->format('H:i');
                        $jamSelesaiTampil = \Carbon\Carbon::parse($jamBlokirSelesai)->format('H:i');
                        return back()->withInput()->with('error',
                            "Booking tidak tersedia pada jam {$jamMulaiTampil} - {$jamSelesaiTampil}{$keterangan}.");
                    }
                }
            }
        }

        // 5. Generate token unik
        do {
            $token = Str::random(32);
        } while (BookingFutsal::where('access_token', $token)->exists());

        // Create dummy user to fulfill DB constraint on booking table
        $user = User::where('no_hp', $request->no_hp)->first();
        if (!$user) {
            $user = User::create([
                'name'         => explode(' ', $request->nama_pemesan)[0],
                'username'     => $request->no_hp,
                'nama_lengkap' => $request->nama_pemesan,
                'email'        => $request->no_hp.'@gmail.com',
                'no_hp'        => $request->no_hp,
                'password'     => bcrypt(Str::random(16)),
            ]);
            $role = \App\Models\Role::where('nama', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }
        $userId = $user->id;

        DB::beginTransaction();
        try {
            $isMembership = $request->input('jenis_pembayaran') === 'membership' && !$isEvent;
            
            // Jika membership, potong kuota langsung dan set status dikonfirmasi
            $statusBooking = $isMembership ? 'dikonfirmasi' : 'menunggu';
            
            // 6. Buat record di tabel booking terlebih dahulu
            $booking = Booking::create([
                'user_id'      => $userId,
                'status'       => $statusBooking,
                'access_token' => $token,
            ]);

            // 7. Buat BookingFutsal
            $bookingFutsal = BookingFutsal::create([
                'booking_id'       => $booking->id,
                'lapangan_id'      => $request->lapangan_id,
                'nama_pemesan'     => $request->nama_pemesan,
                'no_hp'            => $request->no_hp,
                'start_datetime'   => $start,
                'end_datetime'     => $end,
                'jenis_pembayaran' => $isMembership ? 'paket' : ($isEvent ? 'event' : 'reguler'),
                'status'           => $statusBooking,
                'access_token'     => $token,
            ]);

            $lapangan = \App\Models\Lapangan::find($request->lapangan_id);

            if ($isMembership) {
                // Potong kuota
                $membership = Membership::where('user_id', $userId)->where('status', 'aktif')->orderBy('created_at', 'desc')->lockForUpdate()->first();
                if ($membership) {
                    $membership->gunakanKuota($request->durasi);
                }

                // Tetap buat PembayaranFutsal agar muncul di manajemen transaksi admin
                \App\Models\PembayaranFutsal::create([
                    'booking_id' => $booking->id,
                    'tipe_pembayaran_id' => 4, // Tipe Membership
                    'jumlah_bayar' => 0, // Sudah bayar via membership
                    'jenis_transaksi' => 'booking',
                    'status' => 'verifikasi', // Langsung verifikasi karena pakai kuota
                    'tgl_bayar' => now()
                ]);
            } else {
                $pengaturan = \App\Models\Pengaturan::first();
                $tipePembayaran = $request->input('tipe_pembayaran_id') ?? ($request->input('metode_pembayaran') === 'transfer' ? 3 : 2);
                
                if ($isEvent) {
                    $hargaEvent = $pengaturan->harga_event_futsal ?? 800000;
                    $diffDays = $start->startOfDay()->diffInDays($end->startOfDay()) + 1;
                    $totalHarga = $hargaEvent * $diffDays;
                } else {
                    $hargaPerJam = $pengaturan->harga_reguler_futsal ?? 75000;
                    $totalHarga = $hargaPerJam * $request->durasi;
                }
                
                $buktiPath = null;
                if ($request->hasFile('bukti_pembayaran')) {
                    $buktiPath = $request->file('bukti_pembayaran')
                        ->store('bukti_pembayaran_futsal', 'public');
                }

                \App\Models\PembayaranFutsal::create([
                    'booking_id'        => $booking->id,
                    'tipe_pembayaran_id' => $tipePembayaran,
                    'jumlah_bayar'      => $totalHarga,
                    'jenis_transaksi'   => $isEvent ? 'event' : 'booking',
                    'status'            => 'menunggu',
                    'bukti'             => $buktiPath,
                ]);
            }

            DB::commit();

            // 8. Kirim Notifikasi WA
            $apiToken = env('FONNTE_TOKEN');
            $noHp = preg_replace('/[^0-9]/', '', $request->no_hp);
            $namaPemesan = $request->nama_pemesan;
            $namaLapangan = $lapangan->nama;
            $linkAccess = route('user.token.show', ['token' => $token]);

            if ($isEvent) {
                $waktu = $start->translatedFormat('d M Y') . " s/d " . $end->translatedFormat('d M Y');
                $detailBooking = "Jenis: *Event (Multi-hari)*\n📅 Tanggal: *{$waktu}*\n⏰ Waktu: *Full Day*";
            } else {
                $waktu = $start->format('d/m/Y') . " (Jam " . $start->format('H:i') . " - " . $end->format('H:i') . ")";
                $detailBooking = "Jenis: *Reguler*\n📅 Waktu: *{$waktu}*";
            }

            $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
            $pesan .= "Booking lapangan futsal Anda telah berhasil dibuat. ✅\n\n";
            $pesan .= "📋 *Detail Booking:*\n";
            $pesan .= "🏟️ Lapangan: *{$namaLapangan}*\n";
            $pesan .= "{$detailBooking}\n";
            $metodeLabel = $isMembership ? 'Paket Booking' : ($request->input('metode_pembayaran') === 'transfer' ? 'QRIS' : ($request->input('metode_pembayaran') === 'tunai' ? 'Tunai' : ucfirst($request->input('metode_pembayaran'))));
            $pesan .= "💰 Pembayaran: *{$metodeLabel}*\n";
            $pesan .= "📍 Status: *" . ($isMembership ? 'Dikonfirmasi (Lunas)' : 'Menunggu Verifikasi') . "*\n\n";
            $pesan .= "Silahkan akses link di bawah ini untuk melihat detail, status, dan bukti booking Anda:\n";
            $pesan .= "🔗 {$linkAccess}\n\n";
            $pesan .= "Saat membuka link, Anda akan diminta verifikasi OTP yang dikirim otomatis ke WhatsApp ini.\n\n";
            $pesan .= "Terima kasih telah berolahraga di BLUD Futsal! 🙏";

            try {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target'      => $noHp,
                    'message'     => $pesan,
                    'countryCode' => '62',
                    'token'       => $apiToken
                ]);
            } catch (\Exception $e) {
                \Log::warning('Gagal kirim WA booking: ' . $e->getMessage());
            }

            // 9. Return redirect
            return redirect()->route('user.futsal.landing')
                ->with('success', 'Booking berhasil dibuat! 🎉 Kami telah mengirimkan link detail booking ke WhatsApp Anda. Klik link tersebut untuk mengakses informasi booking Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
