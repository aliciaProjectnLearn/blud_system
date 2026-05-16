<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengeluaranAc;
use App\Models\BookingAc;
use App\Models\User;
use App\Models\PenggajianTeknisi;
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
                DB::raw("CONCAT('[', COALESCE(kategori, 'Umum'), '] ', deskripsi) as deskripsi"),
                'nominal',
                DB::raw("'dibayar' as status")
            );

        // 3. Filter Tanggal & Kategori
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $pemasukan->whereBetween(DB::raw('DATE(COALESCE(tgl_bayar, created_at))'), [$startDate, $endDate]);
            $pengeluaran->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($request->filled('kategori')) {
            $pengeluaran->where('kategori', $request->kategori);
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
            'kategori' => 'required|string'
        ]);

        PengeluaranAc::create([
            'deskripsi' => $request->deskripsi,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
        ]);

        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }

    /**
     * Halaman Penggajian Teknisi (Daftar Pekerjaan Belum Dibayar)
     */
    public function payrollIndex(Request $request)
    {
        $teknisiId = $request->teknisi_id;
        $teknisis = User::whereHas('roles', fn($q) => $q->where('nama', 'Teknisi'))->get();

        $pekerjaanUnpaid = BookingAc::with(['layanan', 'pembayaran', 'teknisi'])
            ->where('status', 'selesai')
            ->whereHas('pembayaran', fn($q) => $q->where('status', 'dibayar'))
            ->whereDoesntHave('penggajian')
            ->when($teknisiId, fn($q) => $q->where('teknisi_id', $teknisiId))
            ->get();

        return view('adminac.keuangan.payroll', compact('pekerjaanUnpaid', 'teknisis', 'teknisiId'));
    }

    /**
     * Proses Pembayaran Gaji Teknisi
     */
    public function storePayroll(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|array',
            'booking_id.*' => 'exists:booking_ac,id',
            'nominal' => 'required|array',
            'nominal.*' => 'numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->booking_id as $key => $bookingId) {
                $booking = BookingAc::findOrFail($bookingId);
                $nominal = $request->nominal[$key];

                if ($nominal > 0) {
                    PenggajianTeknisi::create([
                        'teknisi_id' => $booking->teknisi_id,
                        'booking_ac_id' => $booking->id,
                        'nominal' => $nominal,
                        'status_bayar' => 'dibayar',
                        'tanggal_bayar' => now(),
                    ]);

                    // Catat ke Pengeluaran AC agar masuk ke laporan keuangan
                    PengeluaranAc::create([
                        'deskripsi' => "Gaji Teknisi: {$booking->teknisi->name} (Booking #{$booking->id})",
                        'nominal' => $nominal,
                        'tanggal' => now(),
                        'kategori' => 'Gaji Teknisi',
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Gaji teknisi berhasil diproses dan dicatat sebagai pengeluaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());

        }
    }
}
