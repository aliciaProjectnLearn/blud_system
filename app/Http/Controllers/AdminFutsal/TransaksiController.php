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

    public function konfirmasi($id)
    {
        $transaksi = PembayaranFutsal::findOrFail($id);
        if ($transaksi->status === 'menunggu') {
            $transaksi->status = 'verifikasi';
            $transaksi->save();

            // Opsional: jika ingin mengupdate status booking, tambahkan logika di sini.
            // if ($transaksi->booking && $transaksi->booking->status == 'menunggu') {
            //     $transaksi->booking->status = 'dikonfirmasi';
            //     $transaksi->booking->save();
            // }

            return redirect()->route('adminfutsal.transaksi.show', $id)
                ->with('success', 'Pembayaran berhasil dikonfirmasi.');
        }

        return redirect()->route('adminfutsal.transaksi.show', $id)
            ->with('error', 'Status pembayaran tidak dapat diubah (sudah tidak menunggu).');
    }
}
