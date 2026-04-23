<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\PembayaranServis;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    /**
     * Tampilkan daftar transaksi servis (Read-only)
     */
    public function index(Request $request)
    {
        $query = PembayaranServis::with(['bookingServis.pelanggan']);

        // Filter Tanggal dari
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }

        // Filter Tanggal sampai
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        // Filter Status (Pemetaan status di UI: pending, dibayar, gagal)
        if ($request->filled('status')) {
            if ($request->status == 'pending') {
                $query->whereIn('status_pembayaran', ['belum_bayar', 'dp']);
            } elseif ($request->status == 'dibayar') {
                $query->where('status_pembayaran', 'lunas');
            }
            // Status 'gagal' belum ada di enum DB saat ini, jadi tidak diproses filter-nya atau biarkan kosong
        }

        // Search Nama Pelanggan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('bookingServis.pelanggan', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $transaksi = $query->latest('tanggal_bayar')
            ->paginate(15)
            ->withQueryString();

        return view('adminservis.transaksi.index', compact('transaksi'));
    }

    /**
     * Tampilkan detail transaksi (Read-only)
     */
    public function show($id)
    {
        $transaksi = PembayaranServis::with([
            'bookingServis.pelanggan',
            'bookingServis.rincianServis.produkServis',
            'bookingServis.layananServis'
        ])->findOrFail($id);

        return view('adminservis.transaksi.show', compact('transaksi'));
    }
}
