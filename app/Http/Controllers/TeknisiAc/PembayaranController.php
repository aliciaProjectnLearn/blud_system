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
        $pekerjaan = BookingAc::with(['user', 'layanan', 'detailServis', 'pembayaran'])->findOrFail($id);

        // Validation: Booking must belong to the logged-in technician
        if ($pekerjaan->teknisi_id !== Auth::id()) {
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

        // Calculate total: Layanan price + (Spareparts prices if any)
        // Usually, total is the sum of total_harga in pembayaran_ac, or calculated from services. 
        // We'll calculate it from `layanan` and `detail_servis`.
        $totalLayanan = $pekerjaan->layanan->harga_jasa ?? 0;

        
        // For AC, if there's no pre-calculated price in detail_servis, we use product prices.
        // If detail_servis doesn't have prices recorded, we'll try to find matching product.
        // Since detail_servis doesn't store price, we'll assume a basic logic:
        $totalSparepart = 0;
        foreach($pekerjaan->detailServis as $detail) {
            if ($detail->item !== 'Tindakan Servis (Tanpa Sparepart)') {
                $totalSparepart += $detail->subtotal;
            }
        }
        
        $totalTagihan = $totalLayanan + $totalSparepart;
        
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
        $pekerjaan = BookingAc::findOrFail($id);

        if ($pekerjaan->teknisi_id !== Auth::id() || $pekerjaan->status !== 'selesai') {
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
            $pembayaran = PembayaranAc::firstOrNew(['booking_id' => $id]);
            
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

            DB::commit();

            // Send WA Notification
            if (!empty($pekerjaan->no_hp)) {
                $this->kirimWaFonnteSelesai($pekerjaan);
            }

            return redirect()->route('teknisi.dashboard')->with('success', 'Pembayaran berhasil dikonfirmasi dan notifikasi WA telah dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function kirimWaFonnteSelesai($pekerjaan)
    {
        try {
            $pesan = "Halo {$pekerjaan->nama_pelanggan},\n\nTerima kasih telah melakukan Service AC bersama BLUD SMKN 1 Cirebon.\nPembayaran Anda untuk layanan *{$pekerjaan->layanan->nama}* telah kami terima (Lunas).\n\nSemoga layanan kami memuaskan. Jika ada kendala, jangan ragu untuk menghubungi kami kembali.\n\nCek kembali rincian layanan Anda di:\n" . route('user.ac.booking.detail', $pekerjaan->access_token) . "\n\nTerima Kasih!\n*BLUD SMKN 1 Cirebon*";
            
            $apiToken = env('FONNTE_TOKEN', 'YOUR_API_TOKEN_HERE'); 

            if ($apiToken !== 'YOUR_API_TOKEN_HERE') {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $pekerjaan->no_hp,
                    'message' => $pesan,
                    'countryCode' => '62',
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Fonnte Error: ' . $e->getMessage());
        }
    }
}
