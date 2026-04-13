<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranFutsal;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranFutsal::with('booking.user')->orderBy('created_at', 'desc');

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
        $transaksi = PembayaranFutsal::with(['booking.user', 'booking.bookingFutsal.lapangan'])->findOrFail($id);
        return view('adminfutsal.transaksi.show', compact('transaksi'));
    }

    public function konfirmasi(Request $request, $id)
    {
        $transaksi = PembayaranFutsal::with('booking.bookingFutsal')->findOrFail($id);

        if ($transaksi->status === 'menunggu') {
            
            // Jika membership, ambil harga otomatis dari paket
            if ($transaksi->jenis_transaksi === 'membership') {
                $membership = \App\Models\Membership::where('user_id', $transaksi->booking->user_id)
                    ->with('paket')
                    ->latest()
                    ->first();
                $jumlahBayar = $membership->paket->harga ?? 0;
            } else {
                // Reguler/booking → validasi input manual
                $request->validate([
                    'jumlah_bayar' => 'required|numeric|min:1',
                ]);
                $jumlahBayar = $request->jumlah_bayar;
            }

            $transaksi->status = 'verifikasi';
            $transaksi->jumlah_bayar = $jumlahBayar;
            $transaksi->save();

            if ($transaksi->booking && $transaksi->booking->status == 'menunggu') {
                $transaksi->booking->status = 'dikonfirmasi';
                $transaksi->booking->save();
                \Log::info('Booking status updated: ' . $transaksi->booking->fresh()->status);
            }

            return redirect()->route('adminfutsal.transaksi.show', $id)
                ->with('success', 'Pembayaran berhasil dikonfirmasi.');
        }

        return redirect()->route('adminfutsal.transaksi.show', $id)
            ->with('error', 'Status pembayaran tidak dapat diubah.');
    }
}
