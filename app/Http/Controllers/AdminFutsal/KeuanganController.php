<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranFutsal;
use App\Models\PengeluaranFutsal;

class KeuanganController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Keuangan dan daftar transaksi beserta filter
     */
    public function index(Request $request)
    {
        $tglMulai = $request->input('tgl_mulai');
        $tglAkhir = $request->input('tgl_akhir');
        $tipeTransaksi = $request->input('tipe_transaksi'); // 'Pemasukan', 'Pengeluaran', atau kosong untuk semua

        $transaksiGabungan = collect();
        $totalPemasukan = 0;
        $totalPengeluaran = 0;

        // 1. Ambil data Pemasukan jika filter tipe_transaksi kosong atau 'Pemasukan'
        if (!$tipeTransaksi || $tipeTransaksi == 'Pemasukan') {
            $pemasukanQuery = PembayaranFutsal::where('status', 'verifikasi');
            
            if ($tglMulai && $tglAkhir) {
                $pemasukanQuery->whereBetween('tgl_bayar', [$tglMulai, $tglAkhir]);
            }

            // Hitung total dari query
            $totalPemasukan = $pemasukanQuery->sum('jumlah_bayar');

            $pemasukan = $pemasukanQuery->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal_transaksi' => $item->tgl_bayar,
                    'kode_transaksi' => $item->kode_pembayaran,
                    'nominal' => $item->jumlah_bayar,
                    'deskripsi' => 'Pembayaran Futsal', // Keterangan default pemasukan
                    'tipe_transaksi' => 'Pemasukan',
                ];
            });

            $transaksiGabungan = $transaksiGabungan->concat($pemasukan);
        }

        // 2. Ambil data Pengeluaran jika filter tipe_transaksi kosong atau 'Pengeluaran'
        if (!$tipeTransaksi || $tipeTransaksi == 'Pengeluaran') {
            $pengeluaranQuery = PengeluaranFutsal::query();
            
            if ($tglMulai && $tglAkhir) {
                $pengeluaranQuery->whereBetween('tgl_pengeluaran', [$tglMulai, $tglAkhir]);
            }

            // Hitung total dari query
            $totalPengeluaran = $pengeluaranQuery->sum('nominal');

            $pengeluaran = $pengeluaranQuery->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal_transaksi' => $item->tgl_pengeluaran,
                    'kode_transaksi' => $item->kode_pengeluaran,
                    'nominal' => $item->nominal,
                    'deskripsi' => $item->deskripsi,
                    'tipe_transaksi' => 'Pengeluaran',
                ];
            });

            $transaksiGabungan = $transaksiGabungan->concat($pengeluaran);
        }

        // 4. Urutkan berdasarkan tanggal_transaksi secara descending (terbaru di atas)
        $transaksiGabungan = $transaksiGabungan->sortByDesc('tanggal_transaksi')->values();

        // 5. Hitung saldo akhir
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        // 6. Return view dengan membawa data
        return view('adminfutsal.keuangan.index', compact(
            'transaksiGabungan',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoAkhir'
        ));
    }

    /**
     * Memproses form tambah pengeluaran baru
     */
    public function storePengeluaran(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'deskripsi' => 'required|string|max:255',
        ], [
            'tgl_pengeluaran.required' => 'Tanggal pengeluaran wajib diisi.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
        ]);

        // 2. Simpan data pengeluaran baru
        PengeluaranFutsal::create([
            'kode_pengeluaran' => 'PGL-' . date('YmdHis'), // Atau gunakan observer/boot method
            'tgl_pengeluaran' => $request->tgl_pengeluaran,
            'nominal' => $request->nominal,
            'deskripsi' => $request->deskripsi,
        ]);

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan!');
    }
}
