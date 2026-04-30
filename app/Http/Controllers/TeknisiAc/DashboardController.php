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
        $historiPekerjaan = BookingAc::with(['user', 'layanan', 'pembayaran'])
            ->where('teknisi_id', $userId)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Jumlah pekerjaan selesai bulan ini
        $totalSelesaiBulanIni = BookingAc::where('teknisi_id', $userId)
            ->where('status', 'selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        return view('teknisiac.dashboard.index', compact('pekerjaanAktif', 'historiPekerjaan', 'totalSelesaiBulanIni'));
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
        $layanans = \App\Models\LayananAc::all();

        return view('teknisiac.dashboard.show', compact('pekerjaan', 'produks', 'layanans'));
    }

    public function selesaikanPekerjaan(Request $request, $id)
    {
        $pekerjaan = BookingAc::findOrFail($id);

        // Validasi akses
        if ($pekerjaan->teknisi_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'foto_hasil' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $fotoPath = null;
            if ($request->hasFile('foto_hasil')) {
                $fotoPath = $request->file('foto_hasil')->store('dokumentasi_ac', 'public');
            }

            // Simpan Layanan Tambahan yang dipilih teknisi
            if ($request->has('layanan_id')) {
                foreach ($request->layanan_id as $key => $layananId) {
                    if (!empty($layananId)) {
                        $layanan = \App\Models\LayananAc::find($layananId);
                        if ($layanan) {
                            $qty = $request->layanan_qty[$key] ?? 1;
                            $harga = $layanan->harga_jasa ?? 0;
                            $subtotal = $harga * $qty;

                            DetailServis::create([
                                'booking_id' => $pekerjaan->id,
                                'item'       => $layanan->nama ?? 'Layanan AC',
                                'satuan'     => 'Unit/Tindakan',
                                'quantity'   => $qty,
                                'harga'      => $harga,
                                'subtotal'   => $subtotal,
                                'catatan'    => $request->layanan_catatan[$key] ?? null,
                            ]);
                        }
                    }
                }
            }

            // Simpan Sparepart/Produk yang dipilih teknisi
            if ($request->has('produk_id')) {
                foreach ($request->produk_id as $key => $produkId) {
                    $qty = $request->quantity[$key] ?? 1;
                    $catatan = $request->catatan[$key] ?? null;

                    if (!empty($produkId)) {
                        $produk = \App\Models\Produk::findOrFail($produkId);
                        if ($produk->stok < $qty) {
                            throw new \Exception("Stok tidak mencukupi untuk item: {$produk->nama_produk}");
                        }
                        $produk->decrement('stok', $qty);

                        $harga = $produk->harga ?? 0;
                        $subtotal = $harga * $qty;

                        DetailServis::create([
                            'booking_id' => $pekerjaan->id,
                            'item'       => $produk->nama_produk,
                            'satuan'     => $produk->satuan ?? 'Pcs',
                            'quantity'   => $qty,
                            'harga'      => $harga,
                            'subtotal'   => $subtotal,
                            'catatan'    => $catatan,
                        ]);
                    } else {
                        if (!empty($catatan)) {
                            // Jika teknisi menulis catatan tetapi tidak memilih sparepart, ini dianggap free note
                            DetailServis::create([
                                'booking_id' => $pekerjaan->id,
                                'item'       => 'Tindakan Tambahan',
                                'satuan'     => '-',
                                'quantity'   => $qty,
                                'harga'      => 0,
                                'subtotal'   => 0,
                                'catatan'    => $catatan,
                            ]);
                        }
                    }
                }
            }

            // Update status pekerjaan menjadi selesai & simpan foto
            $pekerjaan->update([
                'status' => 'selesai',
                'foto_hasil' => $fotoPath
            ]);

            DB::commit();

            return redirect()->route('teknisi.dashboard')->with('success', 'Pekerjaan berhasil diselesaikan dan dokumentasi telah disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
