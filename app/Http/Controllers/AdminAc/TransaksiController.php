<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\BookingAc;
use App\Models\PembayaranAc;
use App\Models\DetailServis;
use App\Models\TipePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Daftar Transaksi Pembayaran AC
     */
    public function index(Request $request)
    {
        $status = $request->status;
        $search = $request->search;

        $transaksis = PembayaranAc::with(['bookingAc.user', 'tipePembayaran'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%$search%")
                  ->orWhereHas('bookingAc.user', fn($u) => $u->where('name', 'like', "%$search%"));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('adminac.transaksi.index', compact('transaksis', 'status'));
    }

    /**
     * Detail Transaksi / Tampilan Invoice
     */
    public function show($id)
    {
        $transaksi = PembayaranAc::with(['bookingAc.user', 'bookingAc.teknisi', 'tipePembayaran', 'detailServis'])
            ->findOrFail($id);

        return view('adminac.transaksi.show', compact('transaksi'));
    }

    /**
     * Buat Transaksi dari Booking yang Selesai
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:booking_ac,id',
        ]);

        $booking = BookingAc::findOrFail($request->booking_id);

        // Validasi: Harus berstatus 'selesai'
        if ($booking->status !== 'selesai') {
            return back()->with('error', 'Transaksi hanya bisa dibuat untuk booking yang sudah selesai.');
        }

        // Cek jika transaksi sudah ada
        $existing = PembayaranAc::where('booking_id', $booking->id)->first();
        if ($existing) {
            return redirect()->route('admin.ac.transaksi.show', $existing->id)
                ->with('info', 'Invoice sudah tersedia untuk booking ini.');
        }

        // Hitung total harga dari detail_servis
        $totalHarga = DetailServis::where('booking_id', $booking->id)->sum('subtotal');

        if ($totalHarga <= 0) {
            return back()->with('error', 'Detail servis belum diisi atau total biaya nol. Harap isi detail servis terlebih dahulu.');
        }

        // Simpan Transaksi
        $transaksi = PembayaranAc::create([
            'booking_id'         => $booking->id,
            'invoice_no'         => PembayaranAc::generateInvoiceNo(),
            'total_harga'        => $totalHarga,
            'tipe_pembayaran_id' => 1, // Default Transfer Bank
            'status'             => 'pending',
        ]);

        return redirect()->route('admin.ac.transaksi.show', $transaksi->id)
            ->with('success', 'Invoice berhasil dibuat secara otomatis.');
    }

    /**
     * Update Status Pembayaran
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,dibayar,ditolak',
        ]);

        $transaksi = PembayaranAc::findOrFail($id);
        $transaksi->update([
            'status'    => $request->status,
            'tgl_bayar' => $request->status === 'dibayar' ? now() : $transaksi->tgl_bayar,
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Histori Transaksi (Sama dengan Index tapi mungkin dengan filter default Dibayar)
     */
    public function history()
    {
        $transaksis = PembayaranAc::with(['bookingAc.user', 'tipePembayaran'])
            ->where('status', 'dibayar')
            ->orderBy('tgl_bayar', 'desc')
            ->paginate(10);

        return view('adminac.transaksi.history', compact('transaksis'));
    }
}
