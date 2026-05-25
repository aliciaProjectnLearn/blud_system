<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SewaRuko;
use App\Models\Ruko;
use App\Models\Penyewa;
use App\Models\DokumenSewa;
use App\Models\PembayaranRuko;
use App\Models\User;
use App\Http\Requests\AdminKantin\UpdatePenyewaanRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PenyewaanController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // 1. Ambil semua data untuk auto-status logic
        $allData = SewaRuko::with('ruko')->get();

        foreach ($allData as $item) {
            // HANYA tutup masa penyewaan jika sedang aktif dan masa berlakunya kedaluwarsa.
            // JANGAN pernah meng-overwrite manual status admin (terutama 'selesai') menjadi 'aktif' kembali.
            if ($item->status_sewa === 'aktif' && $today->gt(Carbon::parse($item->tanggal_selesai_sewa))) {
                $item->update(['status_sewa' => 'selesai']);
            }

            // UPDATE STATUS RUKO (Source of truth is 'status' column)
            if ($item->status_sewa === 'aktif') {
                $item->ruko->update(['status' => 'disewa']);
            } else {
                // Cek apakah ada penyewaan aktif lain untuk ruko ini
                $masihDisewa = SewaRuko::where('ruko_id', $item->ruko_id)
                    ->whereIn('status_sewa', ['aktif', 'pending', 'proses', 'disetujui'])
                    ->where('id', '!=', $item->id)
                    ->exists();

                if (!$masihDisewa) {
                    $item->ruko->update(['status' => 'tersedia']);
                }
            }
        }

        // 2. Query data dengan filter
        $query = SewaRuko::with(['user', 'ruko', 'ruko.kategori', 'pembayaran']);

        if ($request->status) {
            $query->where('status_sewa', $request->status);
        }

        if ($request->ruko_id) {
            $query->where('ruko_id', $request->ruko_id);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $data = $query->latest()->get();
        $rukos = Ruko::all();
        $penyewas = User::whereHas('sewaRuko')->get(); // Ambil user yang punya riwayat sewa sebagai pengganti daftar penyewa
 
        return view('adminkantin.penyewaan.index', compact('data', 'rukos', 'penyewas'));
    }

    public function show($id)
    {
        $data = SewaRuko::with(['user', 'ruko', 'dokumen', 'pembayaran'])->findOrFail($id);
        return view('adminkantin.penyewaan.show', compact('data'));
    }

    public function update(UpdatePenyewaanRequest $request, $id)
    {
        $sewa = SewaRuko::findOrFail($id);

        DB::beginTransaction();
        try {
            $statusLama = $sewa->status_sewa;

            $sebelum = $sewa->toArray();
            $sewa->update($request->validated());

            // Catat audit
            \App\Services\AuditService::catat(
                'kantin',
                'sewa_ruko',
                $sewa->id,
                'data_diupdate',
                ['nama_penyewa' => $sebelum['nama_penyewa'], 'status_sewa' => $sebelum['status_sewa']],
                ['nama_penyewa' => $sewa->nama_penyewa, 'status_sewa' => $sewa->status_sewa],
                'Data penyewaan diupdate oleh admin'
            );

            // Generate pembayaran saat status berubah jadi disetujui atau aktif
            if ($statusLama === 'pending' && in_array($sewa->status_sewa, ['disetujui', 'aktif'])) {
                $this->generatePembayaranTermin($sewa);
            }

            // Kirim Notifikasi WA saat verifikasi disetujui atau ditolak
            if ($statusLama === 'pending' && $sewa->status_sewa === 'disetujui') {
                $this->sendWhatsAppApproval($sewa);
            } elseif ($statusLama === 'pending' && $sewa->status_sewa === 'ditolak') {
                $this->sendWhatsAppRejection($sewa);
            }

            $this->recalculateStatuses();

            DB::commit();
            return redirect()->back()->with('success', 'Data penyewaan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $sewa = SewaRuko::findOrFail($id);

        if ($sewa->status_sewa !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status Pending yang bisa disetujui.');
        }

        DB::beginTransaction();
        try {
            // Update Status ke Aktif
            $sebelum = $sewa->status_sewa;
            $sewa->update(['status_sewa' => 'aktif']);

            // Catat audit
            \App\Services\AuditService::catat(
                'kantin',
                'sewa_ruko',
                $sewa->id,
                'sewa_disetujui',
                ['status_sewa' => $sebelum],
                ['status_sewa' => 'aktif'],
                'Pengajuan sewa disetujui oleh admin'
            );

            // Update Status Unit Ruko
            $sewa->ruko->update(['status' => 'disewa']); // Pastikan status unit sinkron

            // Generate Pembayaran jika belum ada
            $this->generatePembayaranTermin($sewa);

            $this->recalculateStatuses();

            // Kirim Notifikasi WA
            $this->sendWhatsAppApproval($sewa);

            DB::commit();
            return back()->with('success', 'Pengajuan sewa berhasil disetujui dan notifikasi telah dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui pengajuan: ' . $e->getMessage());
        }
    }

    private function sendWhatsAppApproval($sewa)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) return;

        $link = route('user.kantin.sewa.detail', ['token' => $sewa->access_token]);
        $pesan = "Halo *{$sewa->nama_penyewa}* 👋\n\n";
        $pesan .= "Kabar baik! Pengajuan sewa unit *{$sewa->ruko->kode_unit}* telah *DISETUJUI* oleh Admin.\n\n";
        $pesan .= "Silakan klik link di bawah untuk melihat rincian pembayaran dan mengunduh MOU digital:\n";
        $pesan .= "🔗 {$link}\n\n";
        $pesan .= "Terima kasih telah bergabung bersama kami.\n";
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
            \Illuminate\Support\Facades\Log::error('Gagal Kirim WA Approval: ' . $e->getMessage());
        }
    }

    private function sendWhatsAppRejection($sewa)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) return;

        $pesan = "Halo *{$sewa->nama_penyewa}* 👋\n\n";
        $pesan .= "Mohon maaf, pengajuan sewa unit *{$sewa->ruko->kode_unit}* Anda telah *DITOLAK* oleh Admin.\n\n";
        $pesan .= "Silakan hubungi Admin Kantin BLUD SMK untuk informasi lebih lanjut mengenai alasan penolakan atau untuk mengajukan penyewaan unit lainnya.\n\n";
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
            \Illuminate\Support\Facades\Log::error('Gagal Kirim WA Rejection: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $sewa = SewaRuko::findOrFail($id);

        if ($sewa->status_sewa === 'aktif') {
            return redirect()->back()->with('error', 'Penyewaan yang berstatus aktif tidak dapat dihapus.');
        }

        $sewa->delete();

        return redirect()->route('admin.kantin.penyewaan.index')->with('success', 'Data penyewaan berhasil dihapus.');
    }

    /**
     * Recalculate statuses for all rentals and units.
     */
    private function recalculateStatuses()
    {
        $today = Carbon::today();
        $allData = SewaRuko::with('ruko')->get();

        foreach ($allData as $item) {
            if ($item->status_sewa === 'aktif' && $today->gt(Carbon::parse($item->tanggal_selesai_sewa))) {
                $item->update(['status_sewa' => 'selesai']);
            }

            if (in_array($item->status_sewa, ['aktif', 'disetujui', 'pending', 'proses'])) {
                $item->ruko->update(['status' => 'disewa']);
            } else {
                $masihDisewa = SewaRuko::where('ruko_id', $item->ruko_id)
                    ->whereIn('status_sewa', ['aktif', 'pending', 'proses', 'disetujui'])
                    ->where('id', '!=', $item->id)
                    ->exists();

                if (!$masihDisewa) {
                    $item->ruko->update(['status' => 'tersedia']);
                }
            }
        }
    }
    private function generatePembayaranTermin(SewaRuko $sewa): void
    {
        // Cegah duplikat — jangan generate ulang jika sudah ada
        $sudahAda = \App\Models\PembayaranRuko::where('sewa_ruko_id', $sewa->id)->exists();
        if ($sudahAda) return;

        $tglMulai = Carbon::parse($sewa->tanggal_mulai_sewa);
        $hargaTotal = $sewa->harga_sewa_tahunan;

        // Termin 1: 1 hari setelah tanggal_mulai_sewa
        $jatuhTempoTermin1 = $tglMulai->copy()->addDay();

        if ($sewa->tipe_pembayaran === '1_termin') {
            // Bayar Lunas 100%
            \App\Models\PembayaranRuko::create([
                'sewa_ruko_id'       => $sewa->id,
                'booking_id'         => $sewa->booking_id, // legacy
                'tipe_pembayaran_id' => 1, // default Transfer Bank
                'termin'             => '1', // legacy
                'termin_ke'          => 1,
                'tgl_jatuh_tempo'    => $jatuhTempoTermin1,
                'jumlah_tagihan'     => $hargaTotal,
                'jumlah_bayar'       => 0,
                'status'             => 'menunggu', // legacy
                'status_pembayaran'  => 'pending',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        } else {
            // Bayar 2 Termin (50/50)
            $jumlahPerTermin = intdiv($hargaTotal, 2);
            $jatuhTempoTermin2 = $jatuhTempoTermin1->copy()->addMonths(6);

            \App\Models\PembayaranRuko::insert([
                [
                    'sewa_ruko_id'       => $sewa->id,
                    'booking_id'         => $sewa->booking_id, // legacy
                    'tipe_pembayaran_id' => 1,
                    'termin'             => '1', // legacy
                    'termin_ke'          => 1,
                    'tgl_jatuh_tempo'    => $jatuhTempoTermin1,
                    'jumlah_tagihan'     => $jumlahPerTermin,
                    'jumlah_bayar'       => 0,
                    'status'             => 'menunggu', // legacy
                    'status_pembayaran'  => 'pending',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'sewa_ruko_id'       => $sewa->id,
                    'booking_id'         => $sewa->booking_id, // legacy
                    'tipe_pembayaran_id' => 1,
                    'termin'             => '2', // legacy
                    'termin_ke'          => 2,
                    'tgl_jatuh_tempo'    => $jatuhTempoTermin2,
                    'jumlah_tagihan'     => $hargaTotal - $jumlahPerTermin,
                    'jumlah_bayar'       => 0,
                    'status'             => 'menunggu', // legacy
                    'status_pembayaran'  => 'pending',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
            ]);
        }
    }

    // --- DOCUMENT METHODS ---

    public function generateMOU($id)
    {
        $sewa = SewaRuko::with(['user', 'ruko', 'ruko.kategori'])->findOrFail($id);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'adminkantin.penyewaan.mou-template', 
            compact('sewa')
        )->setPaper('a4', 'portrait');

        // Simpan ke storage
        $filename = 'MOU-' . $sewa->ruko->kode_unit . '-' . 
                    substr($sewa->access_token, 0, 8) . '-' . 
                    now()->format('Ymd') . '.pdf';
        $path = 'dokumen-sewa/' . $filename;
        
        \Illuminate\Support\Facades\Storage::disk('public')->put(
            $path, 
            $pdf->output()
        );

        // Simpan ke tabel dokumen (buat jika belum ada)
        \App\Models\DokumenSewa::updateOrCreate(
            [
                'sewa_ruko_id' => $sewa->id,
                'tipe_dokumen' => 'mou_sistem',
            ],
            [
                'nama_dokumen' => 'MOU - ' . $sewa->ruko->kode_unit,
                'path_file'    => $path,
                'diunggah_oleh' => 'sistem',
                'keterangan'   => 'MOU digenerate otomatis oleh sistem',
            ]
        );

        // Catat audit
        \App\Services\AuditService::catat(
            'kantin',
            'dokumen_sewas',
            $sewa->id,
            'mou_digenerate',
            null,
            ['nama_dokumen' => 'MOU - ' . $sewa->ruko->kode_unit],
            'MOU digenerate otomatis oleh sistem'
        );

        // Download PDF ke browser
        return $pdf->download($filename);
    }

    public function uploadDokumen(Request $request, $id)
    {
        $request->validate([
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'nama_dokumen' => 'required|string|max:255',
            'tipe_dokumen' => 'required|in:mou_hardfile,kwitansi_hardfile,dokumen_lain',
        ]);

        $sewa = SewaRuko::findOrFail($id);

        $file = $request->file('file_dokumen');
        $filename = 'HARDFILE-' . $sewa->ruko->kode_unit . '-' .
                    substr($sewa->access_token, 0, 8) . '-' .
                    now()->format('Ymd') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('dokumen-sewa', $filename, 'public');

        \App\Models\DokumenSewa::create([
            'sewa_ruko_id' => $sewa->id,
            'tipe_dokumen' => $request->tipe_dokumen,
            'nama_dokumen' => $request->nama_dokumen,
            'path_file'    => $path,
            'diunggah_oleh' => 'admin',
            'keterangan'   => $request->keterangan,
        ]);

        // Catat audit
        \App\Services\AuditService::catat(
            'kantin',
            'dokumen_sewas',
            $sewa->id,
            'dokumen_diupload',
            null,
            ['nama_dokumen' => $request->nama_dokumen, 'tipe' => $request->tipe_dokumen],
            'Dokumen hardfile diupload oleh admin'
        );

        return back()->with('success', 'Dokumen berhasil diupload!');
    }

    public function hapusDokumen($id)
    {
        $doc = \App\Models\DokumenSewa::findOrFail($id);

        if (Storage::disk('public')->exists($doc->path_file)) {
            Storage::disk('public')->delete($doc->path_file);
        }

        $doc->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

}
