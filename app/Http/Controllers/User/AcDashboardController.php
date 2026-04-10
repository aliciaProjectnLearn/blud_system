<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingAc;
use App\Models\Booking;
use App\Models\Pembatalan;
use App\Models\LayananAc;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->get('status');

        $query = BookingAc::where('user_id', $user->id)
            ->with(['layanan', 'pembayaran', 'booking']);

        if ($statusFilter) {
            if ($statusFilter === 'canceled') {
                $query->whereHas('booking', function ($q) {
                    $q->where('status', 'dibatalkan');
                });
            } else {
                $query->where('status', $statusFilter)
                    ->whereHas('booking', function ($q) {
                        $q->where('status', '!=', 'dibatalkan');
                    });
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total' => BookingAc::where('user_id', $user->id)->count(),
            'pending' => BookingAc::where('user_id', $user->id)->where('status', 'menunggu')->whereHas('booking', fn($q) => $q->where('status', '!=', 'dibatalkan'))->count(),
            'proses' => BookingAc::where('user_id', $user->id)->where('status', 'proses')->count(),
            'selesai' => BookingAc::where('user_id', $user->id)->where('status', 'selesai')->count(),
        ];

        // Ambil daftar layanan AC untuk ditampilkan di halaman user
        $layanans = LayananAc::with('kategori')->get();

        return view('user.ac.index', compact('bookings', 'stats', 'statusFilter', 'layanans'));
    }

    public function history()
    {
        $user = Auth::user();

        $history = BookingAc::where('user_id', $user->id)
            ->where('status', 'selesai')
            ->with(['layanan', 'teknisi', 'pembayaran', 'booking'])
            ->orderBy('tgl_kunjungan', 'desc')
            ->paginate(10);

        return view('user.ac.history', compact('history'));
    }

    public function show($id)
    {
        $booking = BookingAc::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['layanan', 'teknisi', 'detailServis', 'pembayaran', 'booking'])
            ->firstOrFail();

        // Ensure all fields are included in response
        return response()->json([
            'id' => $booking->id,
            'booking_id' => $booking->booking_id,
            'user_id' => $booking->user_id,
            'layanan_id' => $booking->layanan_id,
            'teknisi_id' => $booking->teknisi_id,
            'tgl_kunjungan' => $booking->tgl_kunjungan,
            'alamat' => $booking->alamat,
            'merek_ac' => $booking->merek_ac,
            'detail_keluhan' => $booking->detail_keluhan,
            'status' => $booking->status,
            'created_at' => $booking->created_at,
            'updated_at' => $booking->updated_at,
            'layanan' => $booking->layanan,
            'teknisi' => $booking->teknisi,
            'pembayaran' => $booking->pembayaran,
            'booking' => $booking->booking,
            'detailServis' => $booking->detailServis,
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        $bookingAc = BookingAc::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($bookingAc->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Booking yang sudah diproses tidak dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Update parent booking status
            $booking = Booking::findOrFail($bookingAc->booking_id);
            $booking->update(['status' => 'dibatalkan']);

            // Create cancellation record
            Pembatalan::create([
                'booking_id' => $booking->id,
                'alasan' => $request->alasan,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan booking. Silakan coba lagi.');
        }
    }

    /**
     * Store a new AC booking (created by pelanggan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan_ac,id',
            'tgl_kunjungan' => 'required|date|after_or_equal:today',
            'alamat' => 'required|string|max:255',
            'merek_ac' => 'nullable|string|max:191',
            'detail_keluhan' => 'nullable|string',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id' => $user->id,
                'status' => 'menunggu',
            ]);

            $bookingAc = BookingAc::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'teknisi_id' => null,
                'layanan_id' => $request->input('layanan_id'),
                'tgl_kunjungan' => $request->input('tgl_kunjungan'),
                'alamat' => $request->input('alamat'),
                'merek_ac' => $request->input('merek_ac'),
                'detail_keluhan' => $request->input('detail_keluhan'),
                'status' => 'menunggu',
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dibuat.',
                    'booking' => $bookingAc,
                ]);
            }

            return redirect()->route('user.ac.index')->with('success', 'Booking berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([ 'success' => false, 'message' => 'Gagal membuat booking.' ], 500);
            }

            return redirect()->back()->with('error', 'Gagal membuat booking. Silakan coba lagi.');
        }
    }

    /**
     * Show layanan listing page for pelanggan (separate page from dashboard)
     */
    public function layanan(Request $request)
    {
        $query = LayananAc::with('kategori');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }

        $layanans = $query->paginate(12)->withQueryString();
        $kategoris = Kategori::where('tipe', 'ac')->get();

        // Return JSON if requested via AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'layanans' => $layanans,
                'kategoris' => $kategoris,
            ]);
        }

        return view('user.ac.layanan', compact('layanans', 'kategoris'));
    }
}
