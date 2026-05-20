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
        // Cari pekerjaan berdasarkan booking_ac.id atau booking_ac.booking_id (mendukung kedua format)
        $pekerjaan = BookingAc::with(['layanan', 'detailServis'])
            ->where('teknisi_id', Auth::id())
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('booking_id', $id);
            })
            ->first();

        if (!$pekerjaan) {
            $pekerjaan = BookingAc::with(['layanan', 'detailServis'])
                ->where('id', $id)
                ->orWhere('booking_id', $id)
                ->firstOrFail();
        }

        $teknisi_id = (int)$pekerjaan->teknisi_id;

        // Validasi 403: Pastikan teknisi hanya bisa melihat pekerjaannya sendiri
        if ($teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pekerjaan ini.');
        }

        $produks = \App\Models\Produk::where('stok', '>', 0)->get();
        $layanans = \App\Models\LayananAc::all();

        return view('teknisiac.dashboard.show', compact('pekerjaan', 'produks', 'layanans'));
    }

    public function selesaikanPekerjaan(Request $request, $id)
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
        // Validasi akses
        if ($teknisi_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'foto_hasil' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'detail_layanan.*' => 'nullable|exists:layanan_ac,id',
            'produk_id.*' => 'nullable|exists:produks,id',
            'quantity_produk.*' => 'nullable|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan Foto Dokumentasi
            if ($request->hasFile('foto_hasil')) {
                $file = $request->file('foto_hasil');
                $filename = time() . '_hasil_' . $pekerjaan->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/ac/hasil'), $filename);
                $pekerjaan->foto_hasil = $filename;
            }

            // 2. Simpan Multiple Layanan
            if ($request->has('detail_layanan')) {
                foreach ($request->detail_layanan as $key => $layananId) {
                    if ($layananId) {
                        $layanan = \App\Models\LayananAc::find($layananId);
                        DetailServis::create([
                            'booking_id' => $pekerjaan->id,
                            'tipe'       => 'layanan',
                            'layanan_id' => $layanan->id,
                            'item'       => $layanan->nama . " (" . $layanan->kapasitas_ac . ")",
                            'satuan'     => 'Unit',
                            'quantity'   => 1,
                            'harga'      => $layanan->harga_jasa,
                            'subtotal'   => $layanan->harga_jasa,
                            'catatan'    => $request->catatan_layanan[$key] ?? null,
                        ]);
                    }
                }
            }

            // 3. Simpan Multiple Sparepart
            if ($request->has('produk_id')) {
                foreach ($request->produk_id as $key => $produkId) {
                    $qty = $request->quantity_produk[$key] ?? 1;
                    if ($produkId) {
                        $produk = \App\Models\Produk::findOrFail($produkId);
                        if ($produk->stok < $qty) {
                            throw new \Exception("Stok tidak mencukupi untuk item: {$produk->nama_produk}");
                        }

                        $produk->decrement('stok', $qty);

                        DetailServis::create([
                            'booking_id' => $pekerjaan->id,
                            'tipe'       => 'sparepart',
                            'produk_id'  => $produk->id,
                            'item'       => $produk->nama_produk,
                            'satuan'     => $produk->satuan ?? 'Pcs',
                            'quantity'   => $qty,
                            'harga'      => $produk->harga,
                            'subtotal'   => $produk->harga * $qty,
                            'catatan'    => $request->catatan_produk[$key] ?? null,
                        ]);
                    }
                }
            }

            // Update status pekerjaan dan layanan utama jika berubah
            $pekerjaan->update([
                'status'     => 'selesai',
                'layanan_id' => $request->detail_layanan[0] ?? $pekerjaan->layanan_id
            ]);

            DB::commit();

            return redirect()->route('teknisi.dashboard')->with('success', 'Pekerjaan berhasil diselesaikan. Silakan lanjut ke proses pembayaran.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
