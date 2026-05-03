<?php

namespace App\Http\Controllers\KasirFutsal;

use App\Http\Controllers\Controller;
use App\Models\BookingFutsal;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BookingKasirFutsalController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingFutsal::with(['lapangan', 'booking.user', 'booking.pembayaranFutsal.tipePembayaran'])
            ->whereIn('status', ['menunggu', 'dikonfirmasi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('start_datetime', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('kasirfutsal.booking.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = BookingFutsal::with(['lapangan', 'booking.user', 'booking.pembayaranFutsal.tipePembayaran'])->findOrFail($id);

        return view('kasirfutsal.booking.show', compact('booking'));
    }

    public function konfirmasi($id)
    {
        $booking = BookingFutsal::with(['lapangan', 'booking.user'])->findOrFail($id);

        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking ini tidak dalam status menunggu.');
        }

        DB::beginTransaction();
        try {
            $booking->status = 'dikonfirmasi';
            $booking->save();

            LogActivity::create([
                'user_id'             => auth()->id(),
                'nama_user'           => auth()->user()->name,
                'sistem'              => 'Futsal',
                'aktivitas'           => 'Konfirmasi Booking',
                'deskripsi_aktivitas' => 'Kasir Futsal mengkonfirmasi booking ID: ' . $booking->id,
            ]);

            // Kirim notifikasi WA via Fonnte
            $noHp = $booking->no_hp ?? null;
            \Log::info('KasirFutsal WA Debug', [
                'booking_id'   => $booking->id,
                'no_hp'        => $noHp,
                'access_token' => $booking->access_token,
                'status_awal'  => $booking->getOriginal('status'),
            ]);
            if ($noHp) {
                try {
                    $apiToken    = env('FONNTE_TOKEN');
                    $namaPemesan = $booking->nama_pemesan ?? ($booking->booking->user->name ?? 'Pelanggan');
                    $lapangan    = $booking->lapangan->nama ?? '-';
                    $tglMain     = \Carbon\Carbon::parse($booking->start_datetime)->translatedFormat('l, d F Y');
                    $jamMulai    = \Carbon\Carbon::parse($booking->start_datetime)->format('H:i');
                    $jamSelesai  = \Carbon\Carbon::parse($booking->end_datetime)->format('H:i');
                    $tokenLink   = url('/user/access/' . $booking->access_token);

                    $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
                    $pesan .= "Booking lapangan futsal Anda telah *dikonfirmasi* ✅\n\n";
                    $pesan .= "📋 *Detail Booking:*\n";
                    $pesan .= "🏟️ Lapangan: *{$lapangan}*\n";
                    $pesan .= "📅 Tanggal : *{$tglMain}*\n";
                    $pesan .= "⏰ Waktu   : *{$jamMulai} - {$jamSelesai}*\n\n";
                    $pesan .= "🔗 Lihat detail booking Anda di:\n{$tokenLink}\n\n";
                    $pesan .= "Simpan link di atas untuk memantau status booking.\n";
                    $pesan .= "Terima kasih 🙏\n— Kasir Futsal BLUD SMK";

                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => $apiToken,
                    ])->post('https://api.fonnte.com/send', [
                        'target'      => $noHp,
                        'message'     => $pesan,
                        'countryCode' => '62',
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Gagal kirim WA konfirmasi kasir futsal: ' . $e->getMessage());
                }
            }

            DB::commit();
            return back()->with('success', 'Booking berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengkonfirmasi booking: ' . $e->getMessage());
        }
    }
}
