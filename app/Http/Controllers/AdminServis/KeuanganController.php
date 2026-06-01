<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\PembayaranServis;
use App\Models\PengeluaranServis;
use App\Models\PenggajianTeknisi;
use App\Models\BookingServis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKeuanganServisExport;

class KeuanganController extends Controller
{
    private function getTransaksiData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tipe = $request->input('tipe');

        $queryPemasukan = PembayaranServis::query();
        $queryPengeluaran = PengeluaranServis::query();

        if ($startDate && $endDate) {
            $queryPemasukan->whereBetween('tanggal_bayar', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
            
            $queryPengeluaran->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $transaksi = collect();

        if (!$tipe || $tipe === 'pemasukan') {
            $pemasukanData = $queryPemasukan->with('bookingServis.user')->latest('tanggal_bayar')->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal_bayar,
                    'tipe' => 'pemasukan',
                    'deskripsi' => 'Pembayaran Servis - ' . ($item->bookingServis->user->name ?? 'Pelanggan'),
                    'nominal' => $item->total_biaya
                ];
            });
            $transaksi = $transaksi->merge($pemasukanData);
        }

        if (!$tipe || $tipe === 'pengeluaran') {
            $pengeluaranData = $queryPengeluaran->latest('tanggal')->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal,
                    'tipe' => 'pengeluaran',
                    'deskripsi' => $item->keterangan,
                    'nominal' => $item->jumlah
                ];
            });
            $transaksi = $transaksi->merge($pengeluaranData);
        }

        return $transaksi->sortByDesc('tanggal')->values();
    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tipe = $request->input('tipe');

        $queryPemasukan = PembayaranServis::query();
        $queryPengeluaran = PengeluaranServis::query();

        if ($startDate && $endDate) {
            $queryPemasukan->whereBetween('tanggal_bayar', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
            
            $queryPengeluaran->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $totalPemasukan = $queryPemasukan->sum('total_biaya');
        $totalPengeluaran = $queryPengeluaran->sum('jumlah');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        $transaksi = $this->getTransaksiData($request);

        $teknisiList = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Teknisi Mobil')->orWhere('nama', 'Teknisi Motor');
        })->get();

        $kasirList = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Kasir');
        })->get();

        return view('adminservis.keuangan.index', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldoAkhir',
            'transaksi',
            'teknisiList',
            'kasirList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:sparepart_produk,gaji_teknisi,gaji_kasir',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'penerima_id' => 'required_if:kategori,gaji_teknisi,gaji_kasir',
            'booking_id' => 'required_if:kategori,gaji_teknisi',
        ]);

        DB::beginTransaction();
        try {
            PengeluaranServis::create([
                'jumlah' => $request->jumlah,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'kategori' => $request->kategori,
                'created_by' => auth()->id()
            ]);

            if ($request->kategori === 'gaji_teknisi') {
                PenggajianTeknisi::create([
                    'teknisi_id' => $request->penerima_id,
                    'booking_servis_id' => $request->booking_id,
                    'nominal' => $request->jumlah,
                    'status_bayar' => 'sudah',
                    'tanggal_bayar' => $request->tanggal,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.servis.keuangan.index')
                ->with('success', 'Data pengeluaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getUnpaidPekerjaan($teknisi_id)
    {
        $bookings = BookingServis::where('teknisi_id', $teknisi_id)
            ->where('status', 'selesai')
            ->whereDoesntHave('penggajianTeknisi', function ($q) {
                $q->where('status_bayar', 'sudah');
            })
            ->with('rincianServis') // Load rincian to calculate estimated pay if needed
            ->get();

        return response()->json($bookings);
    }

    public function exportPdf(Request $request)
    {
        $transaksi = $this->getTransaksiData($request);
        $totalPemasukan = $transaksi->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $transaksi->where('tipe', 'pengeluaran')->sum('nominal');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        $pdf = Pdf::loadView('adminservis.keuangan.pdf', compact('transaksi', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir', 'request'));
        return $pdf->download('laporan_keuangan_servis_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new LaporanKeuanganServisExport($request), 'laporan_keuangan_servis_' . date('Ymd_His') . '.xlsx');
    }
}
