<?php

namespace App\Http\Controllers\TeknisiAc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingAc;
use App\Models\PembayaranAc;
use App\Models\TipePembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    /**
     * Show the payment form for a specific booking.
     */
    public function create($id)
    {
        // Cari pekerjaan berdasarkan booking_ac.id atau booking_ac.booking_id (mendukung kedua format)
        $pekerjaan = BookingAc::with(['user', 'layanan', 'detailServis', 'pembayaran'])
            ->where('teknisi_id', Auth::id())
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('booking_id', $id);
            })
            ->first();

        if (!$pekerjaan) {
            $pekerjaan = BookingAc::with(['user', 'layanan', 'detailServis', 'pembayaran'])
                ->where('id', $id)
                ->orWhere('booking_id', $id)
                ->firstOrFail();
        }

        $teknisi_id = (int)$pekerjaan->teknisi_id;
        // Validation: Booking must belong to the logged-in technician
        if ($teknisi_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Validation: Booking must be finished (selesai)
        if ($pekerjaan->status !== 'selesai') {
            return redirect()->route('teknisi.dashboard')->with('error', 'Pesanan belum selesai dikerjakan.');
        }

        // If payment already verified, return back
        if ($pekerjaan->pembayaran && $pekerjaan->pembayaran->status === 'dibayar') {
            return redirect()->route('teknisi.dashboard')->with('error', 'Pembayaran untuk pesanan ini sudah diverifikasi.');
        }

        // Calculate total: Sum subtotal from all detail_servis records
        $totalTagihan = $pekerjaan->detailServis->sum('subtotal');
        
        // Get common payment methods for technicians: Only Tunai and QRIS
        $metodePembayaran = TipePembayaran::whereRaw('LOWER(nama) LIKE ?', ['%tunai%'])
                                          ->orWhereRaw('LOWER(nama) LIKE ?', ['%qris%'])
                                          ->get();

        return view('teknisiac.dashboard.pembayaran', compact('pekerjaan', 'totalTagihan', 'metodePembayaran'));
    }

    /**
     * Store the payment.
     */
    public function store(Request $request, $id)
    {
        // Cari pekerjaan berdasarkan booking_ac.id atau booking_ac.booking_id (mendukung kedua format)
        $pekerjaan = BookingAc::where('teknisi_id', Auth::id())
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('booking_id', $id);
            })
            ->first();

        if (!$pekerjaan) {
            $pekerjaan = BookingAc::where('id', $id)
                ->orWhere('booking_id', $id)
                ->firstOrFail();
        }

        $teknisi_id = (int)$pekerjaan->teknisi_id;
        if ($teknisi_id !== Auth::id() || $pekerjaan->status !== 'selesai') {
            abort(403);
        }

        $request->validate([
            'tipe_pembayaran_id' => 'required|exists:tipe_pembayaran,id',
            'total_tagihan' => 'required|numeric',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Find or create pembayaran_ac
            $pembayaran = PembayaranAc::firstOrNew(['booking_id' => $pekerjaan->id]);
            
            if (!$pembayaran->exists) {
                $pembayaran->invoice_no = PembayaranAc::generateInvoiceNo();
            }

            // Handle file upload
            if ($request->hasFile('bukti')) {
                // Delete old file if exists (optional but good practice)
                if ($pembayaran->bukti && file_exists(public_path('uploads/pembayaran_ac/' . $pembayaran->bukti))) {
                    unlink(public_path('uploads/pembayaran_ac/' . $pembayaran->bukti));
                }
                
                $file = $request->file('bukti');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pembayaran_ac'), $filename);
                $pembayaran->bukti = $filename;
            }

            $pembayaran->total_harga = $request->total_tagihan;
            $pembayaran->tgl_bayar = now();
            $pembayaran->tipe_pembayaran_id = $request->tipe_pembayaran_id;
            $pembayaran->status = 'dibayar';
            
            $pembayaran->save();

            // 🚀 Kirim WhatsApp Ucapan Terima Kasih
            $this->sendThankYouNotification($pekerjaan);

            DB::commit();

            return redirect()->route('teknisi.dashboard')->with('success', 'Pembayaran berhasil dikonfirmasi dan notifikasi terima kasih telah dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function sendThankYouNotification($booking)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) return;

        $nama = $booking->nama_pelanggan ?? ($booking->user->nama_lengkap ?? 'Pelanggan');
        $noHp = $booking->no_hp ?? ($booking->user->no_hp ?? null);

        if (!$noHp) return;

        $pesan = "*PEMBAYARAN BERHASIL!* ❄️✅\n\n";
        $pesan .= "Halo *{$nama}*,\n\n";
        $pesan .= "Terima kasih telah melakukan pembayaran untuk layanan AC kami. Pekerjaan telah selesai dikerjakan oleh teknisi kami.\n\n";
        $pesan .= "Semoga layanan kami memuaskan. Jika ada keluhan kembali, jangan ragu untuk menghubungi kami.\n\n";
        $pesan .= "Salam,\n*BLUD System*";

        try {
            \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target' => $noHp,
                'message' => $pesan,
                'countryCode' => '62',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Fonnte Thank You Error: " . $e->getMessage());
        }
    }
}
