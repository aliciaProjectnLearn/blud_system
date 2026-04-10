<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengeluaranAc;
use Carbon\Carbon;

class KeuanganAcController extends Controller
{
    /**
     * Menampilkan daftar keuangan (Union Pemasukan dan Pengeluaran)
     */
    public function index(Request $request)
    {
        // 1. Query Pemasukan dari pembayaran_ac
        // Status di database untuk lunas adalah 'dibayar'
        $pemasukan = DB::table('pembayaran_ac')
            ->select(
                DB::raw('COALESCE(tgl_bayar, created_at) as tanggal'),
                DB::raw("'pemasukan' as tipe"),
                DB::raw("CONCAT('Pembayaran Invoice: ', COALESCE(invoice_no, '-')) as deskripsi"),
                'total_harga as nominal',
                'status'
            )
            ->where('status', 'dibayar');

        // 2. Query Pengeluaran dari pengeluaran_ac
        $pengeluaran = DB::table('pengeluaran_ac')
            ->select(
                'tanggal',
                DB::raw("'pengeluaran' as tipe"),
                'deskripsi',
                'nominal',
                DB::raw("'dibayar' as status") // dummy status agar sinkron dengan select diatas
            );

        // 3. Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $pemasukan->whereBetween(DB::raw('DATE(COALESCE(tgl_bayar, created_at))'), [$startDate, $endDate]);
            $pengeluaran->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // 4. Proses Union
        // Gunakan fromSub agar bisa di apply order by dan filter tipe setelah di union
        $query = DB::query()->fromSub($pemasukan->unionAll($pengeluaran), 'keuangan')
                    ->orderBy('tanggal', 'desc');

        // 5. Filter Tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $transaksi = $query->get();

        // 6. Hitung Total & Saldo
        $totalPemasukan = $transaksi->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $transaksi->where('tipe', 'pengeluaran')->sum('nominal');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        return view('adminac.keuangan.index', compact(
            'transaksi', 
            'totalPemasukan', 
            'totalPengeluaran', 
            'saldoAkhir'
        ));
    }

    /**
     * Menyimpan data pengeluaran baru
     */
    public function storePengeluaran(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date'
        ]);

        PengeluaranAc::create([
            'deskripsi' => $request->deskripsi,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }
}
