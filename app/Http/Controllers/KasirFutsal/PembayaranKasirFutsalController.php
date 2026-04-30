<?php

namespace App\Http\Controllers\KasirFutsal;

use App\Http\Controllers\Controller;
use App\Models\PembayaranFutsal;
use App\Models\BookingFutsal;
use App\Models\TipePembayaran;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranKasirFutsalController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranFutsal::query();

        if ($request->filled('status')) {
            if ($request->status == 'lunas') {
                $query->where('status', PembayaranFutsal::STATUS_VERIFIKASI);
            } elseif ($request->status == 'belum lunas') {
                $query->where('status', PembayaranFutsal::STATUS_MENUNGGU);
            }
        }

        $pembayarans = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        // Load manual info booking karena tidak ada relasi langsung di model
        foreach ($pembayarans as $pembayaran) {
            $pembayaran->bookingFutsal = BookingFutsal::with(['user', 'lapangan'])
                ->where('booking_id', $pembayaran->booking_id)
                ->first();
        }

        return view('kasirfutsal.pembayaran.index', compact('pembayarans'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranFutsal::findOrFail($id);
        
        $booking = BookingFutsal::with(['user', 'lapangan'])
            ->where('booking_id', $pembayaran->booking_id)
            ->firstOrFail();

        $tipePembayaran = TipePembayaran::all();

        return view('kasirfutsal.pembayaran.show', compact('pembayaran', 'booking', 'tipePembayaran'));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $pembayaran = PembayaranFutsal::findOrFail($id);

        if ($pembayaran->status === PembayaranFutsal::STATUS_VERIFIKASI) {
            return back()->with('error', 'Pembayaran sudah lunas.');
        }

        $request->validate([
            'tipe_pembayaran_id' => 'required|exists:tipe_pembayaran,id',
        ]);

        DB::beginTransaction();
        try {
            $pembayaran->status = PembayaranFutsal::STATUS_VERIFIKASI;
            $pembayaran->tipe_pembayaran_id = $request->tipe_pembayaran_id;
            $pembayaran->tgl_bayar = now();
            $pembayaran->save();
            
            // update status booking jadi selesai jika lunas (opsional, ikuti instruksi)
            $booking = BookingFutsal::where('booking_id', $pembayaran->booking_id)->first();
            if ($booking && in_array($booking->status, ['dikonfirmasi', 'menunggu'])) {
                $booking->status = 'selesai';
                $booking->save();
            }

        LogActivity::create([
            'user_id'             => auth()->id(),
            'nama_user'           => auth()->user()->name,
            'sistem'              => 'Futsal',
            'aktivitas'           => 'Proses Pembayaran',
            'deskripsi_aktivitas' => 'Kasir Futsal memproses pembayaran ID: ' . $pembayaran->id,
        ]);

            DB::commit();
            return redirect()->route('kasirfutsal.pembayaran.index')->with('success', 'Pembayaran berhasil diproses menjadi lunas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
