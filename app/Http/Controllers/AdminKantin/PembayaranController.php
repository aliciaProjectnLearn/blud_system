<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranRuko;
use App\Models\SewaRuko;
use App\Models\Penyewa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranRuko::with(['sewaRuko.user', 'sewaRuko.ruko'])
            ->whereHas('sewaRuko', function ($q) {
                $q->whereNotIn('status_sewa', ['dibatalkan', 'ditolak']);
            });

        // Filter status
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter termin
        if ($request->filled('termin')) {
            $query->where('termin_ke', $request->termin);
        }

        // Search penyewa
        if ($request->filled('search')) {
            $query->whereHas('sewaRuko', function ($q) use ($request) {
                $q->where('nama_penyewa', 'like', '%' . $request->search . '%');
            });
        }

        $pembayarans = $query->orderBy('sewa_ruko_id')
                             ->orderBy('termin_ke')
                             ->paginate(15);
        
        return view('adminkantin.pembayaran.index', compact('pembayarans'));
    }

    public function show(PembayaranRuko $pembayaran)
    {
        $pembayaran->load([
            'sewaRuko.user',
            'sewaRuko.ruko.kategori',
            'sewaRuko.dokumen',
        ]);
        return view('adminkantin.pembayaran.show', compact('pembayaran'));
    }

    public function update(Request $request, PembayaranRuko $pembayaran)
    {
        // Cegah pembayaran ganda
        if ($pembayaran->status_pembayaran === 'dibayar') {
            return redirect()->back()->with('error', 'Pembayaran ini sudah lunas.');
        }

        $request->validate([
            'tgl_bayar'         => 'required|date',
            'tipe_pembayaran_id' => 'required|exists:tipe_pembayaran,id',
        ]);

        // Generate no kwitansi otomatis
        $noKwitansi = PembayaranRuko::generateNoKwitansi();

        DB::beginTransaction();
        try {
            $sebelum = $pembayaran->status_pembayaran;
            $pembayaran->update([
                'tanggal_bayar'      => $request->tgl_bayar,
                'tipe_pembayaran_id' => $request->tipe_pembayaran_id,
                'status_pembayaran'  => 'dibayar',
                'no_kwitansi'        => $noKwitansi,
            ]);

            // Catat audit
            \App\Services\AuditService::catat(
                'kantin',
                'pembayaran_ruko',
                $pembayaran->id,
                'pembayaran_diverifikasi',
                ['status_pembayaran' => $sebelum],
                ['status_pembayaran' => 'dibayar', 'no_kwitansi' => $noKwitansi],
                'Termin ' . $pembayaran->termin_ke . ' diverifikasi lunas'
            );

            // Jika Termin 1 lunas, aktifkan status sewa
            if ($pembayaran->termin_ke == 1) {
                $sewa = $pembayaran->sewaRuko;
                if ($sewa) {
                    $sewa->update(['status_sewa' => 'aktif']);
                    // Update status unit ruko
                    $sewa->ruko->update(['status_unit' => 'terisi']);
                }
            }

            DB::commit();

            // Auto-generate PDF Kwitansi after Lunas
            $this->generateKwitansiFile($pembayaran);

            // Kirim Notifikasi WA
            $this->sendWhatsAppPaymentSuccess($pembayaran);

            return redirect()->route('admin.kantin.pembayaran.show', $pembayaran)
                ->with('success', "Pembayaran berhasil dikonfirmasi sebagai Lunas. No. Kwitansi: {$noKwitansi}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    private function sendWhatsAppPaymentSuccess(PembayaranRuko $pembayaran)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) return;

        $sewa = $pembayaran->sewaRuko;
        $link = route('user.kantin.sewa.detail', ['token' => $sewa->access_token]);
        $nominal = number_format($pembayaran->jumlah_tagihan, 0, ',', '.');
        
        $pesan = "Halo *{$sewa->nama_penyewa}* 👋\n\n";
        $pesan .= "Pembayaran Anda untuk unit *{$sewa->ruko->kode_unit}* (Termin {$pembayaran->termin_ke}) sebesar *Rp {$nominal}* telah *BERHASIL* diverifikasi.\n\n";
        
        if ($pembayaran->termin_ke == 1) {
            $pesan .= "Status penyewaan Anda sekarang sudah *AKTIF* dan unit dapat digunakan.\n\n";
        }

        $pesan .= "Silakan klik link di bawah untuk melihat rincian penyewaan dan mengunduh kwitansi:\n";
        $pesan .= "🔗 {$link}\n\n";
        $pesan .= "Terima kasih.\n";
        $pesan .= "— Admin Kantin BLUD SMK";

        $target = preg_replace('/[^0-9]/', '', $sewa->no_hp_snapshot);
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }

        try {
            \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $pesan,
                'countryCode' => '62',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal Kirim WA Payment Success: ' . $e->getMessage());
        }
    }

    private function generateKwitansiFile(PembayaranRuko $pembayaran): string
    {
        $pembayaran->loadMissing([
            'sewaRuko.user',
            'sewaRuko.ruko.kategori',
            'tipe',
        ]);

        if (!$pembayaran->no_kwitansi) {
            throw new \Exception("Nomor kwitansi belum tersedia. Pastikan pembayaran sudah dikonfirmasi.");
        }

        $namaFile = 'kwitansi-' . str_replace('/', '-', $pembayaran->no_kwitansi) . '.pdf';
        $pdf = Pdf::loadView('adminkantin.pembayaran.kwitansi', compact('pembayaran'))
            ->setPaper('a5', 'portrait');

        $path = 'kwitansi/' . $namaFile;
        
        try {
            Storage::disk('public')->put($path, $pdf->output());
            $pembayaran->update(['path_kwitansi' => $path]);

            // Tambahkan ke DokumenSewa agar muncul di list dokumen user
            \App\Models\DokumenSewa::updateOrCreate(
                [
                    'sewa_ruko_id' => $pembayaran->sewa_ruko_id,
                    'tipe_dokumen' => 'kwitansi_termin_' . $pembayaran->termin_ke,
                ],
                [
                    'nama_dokumen' => 'Kwitansi Termin ' . $pembayaran->termin_ke . ' - ' . $pembayaran->no_kwitansi,
                    'path_file'    => $path,
                    'diunggah_oleh' => 'sistem',
                    'keterangan'   => 'Kwitansi digenerate otomatis oleh sistem',
                ]
            );

            return $path;
        } catch (\Exception $e) {
            \Log::error("Gagal generate kwitansi ID {$pembayaran->id}: " . $e->getMessage());
            throw new \Exception("Gagal menyimpan file kwitansi ke storage. Silakan hubungi admin IT.");
        }
    }

    public function downloadKwitansi(PembayaranRuko $pembayaran)
    {
        if ($pembayaran->status_pembayaran !== 'dibayar') {
            return redirect()->back()->with('error', 'Kwitansi hanya tersedia untuk pembayaran yang sudah lunas.');
        }

        try {
            $path = $pembayaran->path_kwitansi;

            // Jika path kosong di DB atau file fisik hilang, paksa generate ulang
            if (empty($path) || !Storage::disk('public')->exists($path)) {
                $path = $this->generateKwitansiFile($pembayaran);
            }

            // Validasi final path sebelum didownload untuk mencegah download(null)
            if (empty($path) || !Storage::disk('public')->exists($path)) {
                throw new \Exception("Sistem gagal menemukan atau membuat file kwitansi.");
            }

            $namaDownload = 'kwitansi-' . str_replace('/', '-', $pembayaran->no_kwitansi) . '.pdf';
            return Storage::disk('public')->download($path, $namaDownload);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload kwitansi: ' . $e->getMessage());
        }
    }
}
