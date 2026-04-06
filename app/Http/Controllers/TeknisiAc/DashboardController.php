<?php

namespace App\Http\Controllers\TeknisiAc;

use App\Http\Controllers\Controller;
use App\Models\BookingAc;
use App\Models\DetailServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan daftar tugas mekanik/teknisi.
     */
    public function index()
    {
        $userId = Auth::id();

        // Pekerjaan yang masih aktif (proses)
        $pekerjaanAktif = BookingAc::with(['user', 'layanan'])
            ->where('teknisi_id', $userId)
            ->where('status', 'proses')
            ->orderBy('tgl_kunjungan', 'asc')
            ->get();

        // Riwayat pekerjaan yang sudah selesai
        $historiPekerjaan = BookingAc::with(['user', 'layanan'])
            ->where('teknisi_id', $userId)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('teknisiac.dashboard.index', compact('pekerjaanAktif', 'historiPekerjaan'));
    }

    /**
     * Menampilkan detail dari sebuah pekerjaan.
     */
    public function show($id)
    {
        $pekerjaan = BookingAc::with(['user', 'layanan', 'detailServis'])->findOrFail($id);

        // Validasi 403: Pastikan teknisi hanya bisa melihat pekerjaannya sendiri
        if ($pekerjaan->teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pekerjaan ini.');
        }

        $produks = \App\Models\Produk::where('stok', '>', 0)->get();

        return view('teknisiac.dashboard.show', compact('pekerjaan', 'produks'));
    }

    /**
     * Menyimpan detail servis dan menyelesaikan status pekerjaan.
     */
    public function selesaikanPekerjaan(Request $request, $id)
    {
        $pekerjaan = BookingAc::findOrFail($id);

        // Validasi akses
        if ($pekerjaan->teknisi_id !== Auth::id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            if ($request->has('produk_id')) {
                foreach ($request->produk_id as $key => $produkId) {
                    $qty = $request->quantity[$key] ?? 1;
                    $catatan = $request->catatan[$key] ?? null;

                    if (!empty($produkId)) {
                        // Jika menggunakan produk/sparepart
                        $produk = \App\Models\Produk::findOrFail($produkId);

                        if ($produk->stok < $qty) {
                            throw new \Exception("Stok tidak mencukupi untuk item: {$produk->nama_produk}");
                        }

                        // Kurangi stok di tabel produks
                        $produk->decrement('stok', $qty);

                        // Insert ke tabel detail_servis
                        DetailServis::create([
                            'booking_id' => $pekerjaan->id,
                            'item'       => $produk->nama_produk,
                            'satuan'     => $produk->satuan ?? 'Pcs',
                            'quantity'   => $qty,
                            'catatan'    => $catatan,
                        ]);
                    } else {
                        // Jika "Tidak pakai sparepart" dipilih tapi ada catatan tindakan
                        if (!empty($catatan)) {
                            DetailServis::create([
                                'booking_id' => $pekerjaan->id,
                                'item'       => 'Tindakan Servis (Tanpa Sparepart)',
                                'satuan'     => '-',
                                'quantity'   => $qty,
                                'catatan'    => $catatan,
                            ]);
                        }
                    }
                }
            }

            // Update status pekerjaan menjadi selesai
            $pekerjaan->update([
                'status' => 'selesai'
            ]);

            DB::commit();

            return redirect()->route('teknisi.dashboard')->with('success', 'Pekerjaan berhasil diselesaikan dan detail servis telah disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
