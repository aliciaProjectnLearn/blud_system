<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFutsal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FutsalDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard futsal user
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Ringkasan Data
        $summary = [
            'total' => BookingFutsal::where('user_id', $userId)->whereHas('booking')->count(),
            'aktif' => BookingFutsal::where('user_id', $userId)
                ->whereHas('booking', function ($query) {
                    $query->whereIn('status', ['menunggu', 'dikonfirmasi']);
                })->count(),
            'history' => BookingFutsal::where('user_id', $userId)
                ->whereHas('booking', function ($query) {
                    $query->whereIn('status', ['selesai', 'dibatalkan']);
                })->count(),
        ];

        // 2. Aktivitas Terbaru (5 records)
        $recent = BookingFutsal::with(['booking', 'lapangan'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('user.futsal.dashboard', compact('summary', 'recent'));
    }

    /**
     * Menampilkan halaman histori booking futsal
     */
    public function history(Request $request)
    {
        $userId = Auth::id();
        
        $query = BookingFutsal::with(['booking', 'lapangan'])
            ->where('user_id', $userId)
            ->whereHas('booking');

        // Apply Filters
        if ($request->status && $request->status !== 'all') {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl_main', [$request->start_date, $request->end_date]);
        }

        $history = $query->orderBy('tgl_main', 'desc')->paginate(10);

        return view('user.futsal.history', compact('history'));
    }

    /**
     * API: Mendapatkan detail satu booking (Tetap digunakan untuk Modal)
     */
    public function getDetail($id)
    {
        $userId = Auth::id();
        $booking = BookingFutsal::with(['booking', 'lapangan', 'booking.pembayaranFutsal.tipePembayaran'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->findOrFail($id);

        $pay = $booking->booking->pembayaranFutsal->first();

        return response()->json([
            'id' => $booking->id,
            'lapangan' => [
                'nama' => $booking->lapangan->nama ?? 'Lapangan Tidak Diketahui',
                'ukuran' => $booking->lapangan->ukuran ?? '-',
                'deskripsi' => $booking->lapangan->deskripsi ?? '',
            ],
            'jadwal' => [
                'tanggal' => Carbon::parse($booking->tgl_main)->format('d F Y'),
                'jam' => substr($booking->jam_mulai, 0, 5) . ' - ' . substr($booking->jam_selesai, 0, 5),
                'durasi' => $booking->durasi_main . ' Jam',
            ],
            'status' => [
                'label' => ucfirst($booking->booking->status ?? 'Menunggu'),
                'color' => $this->getStatusColor($booking->booking->status ?? 'menunggu'),
            ],
            'pembayaran' => [
                'jenis' => ucfirst($booking->jenis_pembayaran ?? 'reguler'),
                'total' => 'Rp ' . number_format($pay->jumlah_bayar ?? 0, 0, ',', '.'),
                'status' => ucfirst($pay->status ?? 'Menunggu'),
                'metode' => $pay->tipePembayaran->nama ?? 'Tunai',
            ]
        ]);
    }

    /**
     * Menampilkan Invoice versi HTML (untuk print browser)
     */
    public function showInvoice($id)
    {
        $userId = Auth::id();
        $booking = BookingFutsal::with(['booking', 'lapangan', 'booking.pembayaranFutsal.tipePembayaran', 'user'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->findOrFail($id);

        $pembayaran = $booking->booking->pembayaranFutsal->first();
        $pengaturan = \App\Models\Pengaturan::first();

        return view('user.futsal.invoice', compact('booking', 'pembayaran', 'pengaturan'));
    }

    /**
     * Mengunduh Invoice PDF
     */
    public function downloadInvoice($id)
    {
        $userId = Auth::id();
        $booking = BookingFutsal::with(['booking', 'lapangan', 'booking.pembayaranFutsal.tipePembayaran', 'user'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->findOrFail($id);

        $pembayaran = $booking->booking->pembayaranFutsal->first();
        $pengaturan = \App\Models\Pengaturan::first();

        $pdf = Pdf::loadView('user.futsal.invoice-pdf', compact('booking', 'pembayaran', 'pengaturan'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-Futsal-' . $booking->id . '.pdf');
    }

    private function getStatusColor($status)
    {
        return match (strtolower($status ?? '')) {
            'menunggu' => 'yellow',
            'dikonfirmasi' => 'blue',
            'selesai' => 'green',
            'dibatalkan' => 'red',
            default => 'gray',
        };
    }
}
