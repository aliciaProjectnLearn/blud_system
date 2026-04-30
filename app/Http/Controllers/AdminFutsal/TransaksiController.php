<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranFutsal;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranFutsal::with(['booking.user', 'tipePembayaran'])->orderBy('created_at', 'desc');

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
                $membership = \App\Models\Membership::where('user_id', $transaksi->booking->user_id)
                    ->with('paket')
                    ->latest()
                    ->first();
                $jumlahBayar = $membership->paket->harga ?? 0;
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

            if ($transaksi->booking && $transaksi->booking->status == 'menunggu') {
                $transaksi->booking->status = 'dikonfirmasi';
                $transaksi->booking->save();
            }

            return redirect()->route('adminfutsal.transaksi.show', $id)
                ->with('success', 'Pembayaran berhasil dikonfirmasi.');
        }

        return redirect()->route('adminfutsal.transaksi.show', $id)
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

                // 2. Update status booking utama
                if ($transaksi->booking) {
                    $transaksi->booking->status = 'dibatalkan';
                    $transaksi->booking->save();

                    // 3. Lepaskan jadwal lapangan jika jenisnya booking/reguler
                    $bf = $transaksi->booking->bookingFutsal;
                    if ($bf) {
                        \App\Models\JadwalLapangan::where('lapangan_id', $bf->lapangan_id)
                            ->whereDate('tanggal', \Carbon\Carbon::parse($bf->start_datetime)->toDateString())
                            ->where('jam_mulai', '>=', \Carbon\Carbon::parse($bf->start_datetime)->toTimeString())
                            ->where('jam_mulai', '<', \Carbon\Carbon::parse($bf->end_datetime)->toTimeString())
                            ->update(['status' => 'tersedia']);
                    }
                }
            });

            return redirect()->route('adminfutsal.transaksi.show', $id)
                ->with('success', 'Pembayaran ditolak dan booking dibatalkan.');
        }

        return redirect()->route('adminfutsal.transaksi.show', $id)
            ->with('error', 'Status pembayaran tidak dapat diubah.');
    }
}
