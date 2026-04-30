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
        $pemasukan = DB::table('pembayaran_ac')
            ->select(
                DB::raw('COALESCE(tgl_bayar, created_at) as tanggal'),
                DB::raw("'pemasukan' as tipe"),
                DB::raw("CONCAT('Pembayaran Invoice: ', COALESCE(invoice_no, '-')) as deskripsi"),
                'total_harga as nominal',
                'status',
                DB::raw("'-' as kategori")
            )
            ->where('status', 'dibayar');

        // 2. Query Pengeluaran dari pengeluaran_ac
        $pengeluaran = DB::table('pengeluaran_ac')
            ->select(
                'tanggal',
                DB::raw("'pengeluaran' as tipe"),
                'deskripsi',
                'nominal',
                DB::raw("'dibayar' as status"),
                'kategori'
            );

        // 3. Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $pemasukan->whereBetween(DB::raw('DATE(COALESCE(tgl_bayar, created_at))'), [$startDate, $endDate]);
            $pengeluaran->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // 4. Proses Union
        $query = DB::query()->fromSub($pemasukan->unionAll($pengeluaran), 'keuangan')
                    ->orderBy('tanggal', 'desc');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $transaksi = $query->get();

        // 6. Hitung Total & Saldo
        $totalPemasukan = $transaksi->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $transaksi->where('tipe', 'pengeluaran')->sum('nominal');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        // --- Fitur Gaji Teknisi ---
        $pekerjaanQuery = \App\Models\BookingAc::with(['teknisi', 'layanan'])
            ->where('status', 'selesai')
            ->where('status_gaji', 'belum_dibayar');

        if ($request->filled('teknisi_id')) {
            $pekerjaanQuery->where('teknisi_id', $request->teknisi_id);
        }
        
        $pekerjaanBelumDibayar = $pekerjaanQuery->get();
        $teknisis = \App\Models\User::whereIn('id', \App\Models\BookingAc::whereNotNull('teknisi_id')->distinct()->pluck('teknisi_id'))->get();

        return view('adminac.keuangan.index', compact(
            'transaksi', 
            'totalPemasukan', 
            'totalPengeluaran', 
            'saldoAkhir',
            'pekerjaanBelumDibayar',
            'teknisis'
        ));
    }

    public function storePengeluaran(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'kategori' => 'required|in:sparepart,gaji,lainnya'
        ]);

        PengeluaranAc::create([
            'deskripsi' => $request->deskripsi,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
        ]);

        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }

    public function bayarGaji(Request $request, $booking_id)
    {
        $request->validate([
            'nominal_gaji' => 'required|numeric|min:1',
        ]);

        $booking = \App\Models\BookingAc::findOrFail($booking_id);

        if ($booking->status !== 'selesai' || $booking->status_gaji === 'dibayar') {
            return back()->with('error', 'Pekerjaan ini belum selesai atau gajinya sudah dibayar.');
        }

        DB::beginTransaction();
        try {
            // Update status gaji
            $booking->update([
                'status_gaji' => 'dibayar'
            ]);

            // Catat sebagai pengeluaran AC
            PengeluaranAc::create([
                'deskripsi' => 'Pembayaran Gaji Teknisi (' . ($booking->teknisi->name ?? 'Unknown') . ') untuk pekerjaan ' . ($booking->layanan->nama ?? '-'),
                'nominal' => $request->nominal_gaji,
                'tanggal' => now()->format('Y-m-d'),
                'kategori' => 'gaji',
            ]);

            DB::commit();
            return back()->with('success', 'Gaji teknisi berhasil dibayarkan dan dicatat sebagai pengeluaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membayar gaji: ' . $e->getMessage());
        }
    }
}
