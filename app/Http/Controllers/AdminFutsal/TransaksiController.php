<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranFutsal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranFutsal::with(['booking.user', 'booking.bookingFutsal', 'membership.user', 'tipePembayaran'])->orderBy('created_at', 'desc');

        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksis = $query->get();
            
        return view('adminfutsal.transaksi.index', compact('transaksis'));
    }

    public function show($id)
    {
        $transaksi = PembayaranFutsal::with(['booking.user', 'booking.bookingFutsal.lapangan', 'tipePembayaran'])->findOrFail($id);
        return view('adminfutsal.transaksi.show', compact('transaksi'));
    }

    public function konfirmasi(Request $request, $id)
    {
        $transaksi = PembayaranFutsal::with(['booking.bookingFutsal', 'booking.user'])->findOrFail($id);

        if ($transaksi->status === 'menunggu') {
            
            // Jika membership, ambil harga otomatis dari paket
            if ($transaksi->jenis_transaksi === 'membership') {
                $membership = \App\Models\Membership::where('transaksi_id', $transaksi->id)
                    ->with('paket')
                    ->first();
                $jumlahBayar = $membership->paket->harga ?? $transaksi->jumlah_bayar;
            } else {
                // Reguler/booking → gunakan harga yang sudah terhitung di DB jika ada, atau ambil dari request
                if ($request->filled('jumlah_bayar')) {
                    $jumlahBayar = $request->jumlah_bayar;
                } else {
                    $jumlahBayar = $transaksi->jumlah_bayar;
                }
                
                if ($jumlahBayar <= 0) {
                    return back()->with('error', 'Nominal pembayaran tidak valid.');
                }
            }

            $transaksi->status = 'verifikasi';
            $transaksi->jumlah_bayar = $jumlahBayar;
            $transaksi->save();

            if ($transaksi->jenis_transaksi === 'membership') {
                $membership = \App\Models\Membership::where('transaksi_id', $transaksi->id)->first();
                if ($membership) {
                    $membership->status = 'aktif';
                    $membership->save();
                }
            } else if ($transaksi->booking && $transaksi->booking->status == 'menunggu') {
                $transaksi->booking->status = 'dikonfirmasi';
                $transaksi->booking->save();

                if ($transaksi->booking->bookingFutsal) {
                    $transaksi->booking->bookingFutsal->status = 'dikonfirmasi';
                    $transaksi->booking->bookingFutsal->save();

                    $bookingFutsal = $transaksi->booking->bookingFutsal;
                    $noHp = $bookingFutsal->no_hp ?? null;

                    if ($noHp) {
                        $apiToken = config('services.fonnte.token');
                        $namaPemesan = $bookingFutsal->nama_pemesan ?? 'Pelanggan';
                        $lapangan = $bookingFutsal->lapangan->nama ?? '-';
                        $tglMain = \Carbon\Carbon::parse($bookingFutsal->start_datetime)
                                    ->translatedFormat('l, d F Y');
                        $jamMulai = \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('H:i');
                        $jamSelesai = \Carbon\Carbon::parse($bookingFutsal->end_datetime)->format('H:i');
                        $tokenLink = url('/user/access/' . $bookingFutsal->access_token);

                        $isEvent = ($transaksi->jenis_transaksi === 'event');
                        
                        $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
                        $pesan .= "Booking lapangan futsal Anda telah *dikonfirmasi* ✅\n\n";
                        $pesan .= "📋 *Detail Booking:*\n";
                        $pesan .= "🏟️ Lapangan : *{$lapangan}*\n";

                        if ($isEvent) {
                            $tglSelesai = \Carbon\Carbon::parse($bookingFutsal->end_datetime)->translatedFormat('l, d F Y');
                            $pesan .= "📅 Tanggal  : *{$tglMain}* s/d *{$tglSelesai}*\n";
                            $pesan .= "⏰ Waktu    : *Full Day (Event)*\n\n";
                        } else {
                            $pesan .= "📅 Tanggal  : *{$tglMain}*\n";
                            $pesan .= "⏰ Waktu    : *{$jamMulai} - {$jamSelesai}*\n\n";
                        }

                        $pesan .= "🔗 Lihat detail booking Anda di:\n{$tokenLink}\n\n";
                        $pesan .= "Simpan link di atas untuk memantau status booking, riwayat, atau pembatalan.\n";
                        $pesan .= "Terima kasih 🙏\n— Admin Futsal BLUD SMK";
                        try {
                            \Illuminate\Support\Facades\Http::withHeaders([
                                'Authorization' => $apiToken,
                            ])->post('https://api.fonnte.com/send', [
                                'target'      => $noHp,
                                'message'     => $pesan,
                                'countryCode' => '62',
                            ]);
                        } catch (\Exception $e) {
                            // Gagal kirim WA tidak mengganggu proses konfirmasi
                            \Log::warning('Gagal kirim WA konfirmasi futsal: ' . $e->getMessage());
                        }
                    }
                }
            }

            return redirect()->route('admin.futsal.transaksi.show', $id)
                ->with('success', 'Pembayaran berhasil dikonfirmasi.');
        }

        return redirect()->route('admin.futsal.transaksi.show', $id)
            ->with('error', 'Status pembayaran tidak dapat diubah.');
    }

    public function reject($id)
    {
        $transaksi = PembayaranFutsal::with('booking.bookingFutsal')->findOrFail($id);

        if ($transaksi->status === 'menunggu') {
            \DB::transaction(function() use ($transaksi) {
                // 1. Update status pembayaran
                $transaksi->status = 'dibatalkan';
                $transaksi->save();

                if ($transaksi->jenis_transaksi === 'membership') {
                    $membership = \App\Models\Membership::where('transaksi_id', $transaksi->id)->first();
                    if ($membership) {
                        $membership->status = 'nonaktif';
                        $membership->save();
                    }
                } else if ($transaksi->booking) {
                    // 2. Update status booking utama
                    $transaksi->booking->status = 'dibatalkan';
                    $transaksi->booking->save();

                    // 3. Lepaskan jadwal lapangan jika jenisnya booking/reguler
                    $bf = $transaksi->booking->bookingFutsal;
                    if ($bf) {
                        $bf->status = 'dibatalkan';
                        $bf->save();
                        \App\Models\JadwalLapangan::where('lapangan_id', $bf->lapangan_id)
                            ->whereDate('tanggal', \Carbon\Carbon::parse($bf->start_datetime)->toDateString())
                            ->where('jam_mulai', '>=', \Carbon\Carbon::parse($bf->start_datetime)->toTimeString())
                            ->where('jam_mulai', '<', \Carbon\Carbon::parse($bf->end_datetime)->toTimeString())
                            ->update(['status' => 'tersedia']);
                    }
                }
            });

            return redirect()->route('admin.futsal.transaksi.show', $id)
                ->with('success', 'Pembayaran ditolak.');
        }

        return redirect()->route('admin.futsal.transaksi.show', $id)
            ->with('error', 'Status pembayaran tidak dapat diubah.');
    }
}
