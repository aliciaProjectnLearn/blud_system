<?php

namespace App\Http\Controllers\KasirServis;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use App\Models\RincianServis;
use App\Models\ProdukServis;
use App\Models\PembayaranServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingKasirController extends Controller
{
    /**
     * Tampilkan daftar booking yang perlu diproses kasir
     */
    public function index(Request $request)
    {
        $query = BookingServis::with(['pelanggan', 'layananServis'])
            ->whereIn('status', ['menunggu', 'diproses']);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_booking', $request->tanggal);
        }

        // Search Nama Pelanggan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('tanggal_booking')
            ->paginate(15)
            ->withQueryString();

        // Hitung ketersediaan slot untuk tanggal yang difilter atau hari ini (Card #65 - Status Slot)
        $tanggalSlot = $request->tanggal ?? now()->toDateString();
        $slotUsage = BookingServis::whereDate('tanggal_booking', $tanggalSlot)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->select('jam_booking', DB::raw('count(*) as total'))
            ->groupBy('jam_booking')
            ->pluck('total', 'jam_booking')
            ->toArray();

        return view('kasirservis.booking.index', compact('bookings', 'slotUsage', 'tanggalSlot'));
    }

    /**
     * Tampilkan detail booking dan form input rincian
     */
    public function show($id)
    {
        $booking = BookingServis::with(['pelanggan', 'rincianServis.produkServis', 'layananServis'])
            ->findOrFail($id);
            
        $produk = ProdukServis::all(); // Untuk pilihan sparepart

        return view('kasirservis.booking.show', compact('booking', 'produk'));
    }

    /**
     * Simpan atau update rincian servis berdasarkan input kasir
     */
    public function simpanRincian(Request $request, $id)
    {
        $booking = BookingServis::findOrFail($id);

        // Validasi status
        if (in_array($booking->status, ['selesai', 'batal'])) {
            return back()->with('error', 'Booking dengan status selesai atau batal tidak dapat diubah rinciannya.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.nama_item' => 'required|string',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Hapus rincian lama untuk diganti dengan yang baru (sync manual)
            $booking->rincianServis()->delete();

            foreach ($request->items as $item) {
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $booking->rincianServis()->create([
                    'nama_item' => $item['nama_item'],
                    'produk_servis_id' => $item['produk_servis_id'] ?? null,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Update status booking ke 'diproses' jika sebelumnya 'menunggu'
            if ($booking->status == 'menunggu') {
                $booking->status = 'diproses';
                $booking->save();
            }

            DB::commit();
            return back()->with('success', 'Rincian servis berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan rincian: ' . $e->getMessage());
        }
    }

    /**
     * Validasi rincian dan arahkan ke proses pembayaran
     */
    public function lanjutPembayaran($id)
    {
        $booking = BookingServis::with('rincianServis')->findOrFail($id);

        // Validasi rincian tidak kosong
        if ($booking->rincianServis->isEmpty()) {
            return back()->with('error',
                'Rincian servis masih kosong. Harap isi rincian servis terlebih dahulu.');
        }

        // Validasi status
        if (in_array($booking->status, ['selesai', 'batal', 'siap_bayar'])) {
            return back()->with('error',
                'Status booking tidak memungkinkan untuk lanjut pembayaran.');
        }

        DB::beginTransaction();
        try {
            $totalBiaya = $booking->rincianServis->sum('subtotal');

            // Update status booking ke siap_bayar
            $booking->status = 'siap_bayar';
            $booking->save();

            // Buat atau update record pembayaran
            PembayaranServis::updateOrCreate(
                ['booking_servis_id' => $booking->id],
                [
                    'kode_pembayaran' => $booking->pembayaranServis->kode_pembayaran
                        ?? 'PAY-' . now()->format('YmdHis'),
                    'total_biaya' => $totalBiaya,
                    'status_pembayaran' => 'belum_bayar',
                ]
            );

            DB::commit();
            return redirect()->route('kasir.pembayaran.index')
                ->with('success', 'Booking ' . $booking->kode_booking
                    . ' telah dipindahkan ke antrian pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar booking siap bayar (Menu Pembayaran Kasir)
     */
    public function indexPembayaran(Request $request)
    {
        $query = BookingServis::with(['pelanggan', 'layananServis', 'pembayaranServis'])
            ->where('status', 'siap_bayar');

        // Filter tanggal booking
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_booking', $request->tanggal);
        }

        // Search nama pelanggan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('tanggal_booking')
            ->paginate(15)
            ->withQueryString();

        return view('kasirservis.pembayaran.index', compact('bookings'));
    }

    /**
     * Tampilkan detail booking dan form konfirmasi pembayaran
     */
    public function showPembayaran($id)
    {
        $booking = BookingServis::with([
            'pelanggan',
            'layananServis',
            'rincianServis.produkServis',
            'pembayaranServis'
        ])->findOrFail($id);

        // Validasi: harus status siap_bayar
        if ($booking->status !== 'siap_bayar') {
            return redirect()->route('kasir.pembayaran.index')
                ->with('error', 'Booking ini tidak dalam status siap bayar.');
        }

        // Hitung total dari rincian
        $totalBiaya = $booking->rincianServis->sum('subtotal');

        return view('kasirservis.pembayaran.show', compact('booking', 'totalBiaya'));
    }

    /**
     * Konfirmasi dan proses pembayaran booking
     */
    public function konfirmasiPembayaran(Request $request, $id)
    {
        $booking = BookingServis::with(['rincianServis', 'pembayaranServis'])->findOrFail($id);

        // Validasi: harus status siap_bayar
        if ($booking->status !== 'siap_bayar') {
            return back()->with('error', 'Booking ini tidak dalam status siap bayar.');
        }

        // Validasi: rincian tidak boleh kosong
        if ($booking->rincianServis->isEmpty()) {
            return back()->with('error', 'Rincian servis kosong. Tidak dapat memproses pembayaran.');
        }

        $request->validate([
            'tipe_pembayaran' => 'required|in:tunai,transfer,qris',
            'catatan'         => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $totalBiaya = $booking->rincianServis->sum('subtotal');

            // Update atau buat record pembayaran
            PembayaranServis::updateOrCreate(
                ['booking_servis_id' => $booking->id],
                [
                    'kode_pembayaran'   => $booking->pembayaranServis->kode_pembayaran
                        ?? 'PAY-' . now()->format('YmdHis'),
                    'total_biaya'       => $totalBiaya,
                    'tipe_pembayaran'   => $request->tipe_pembayaran,
                    'status_pembayaran' => 'lunas',
                    'tanggal_bayar'     => now(),
                    'catatan'           => $request->catatan,
                ]
            );

            // Update status booking menjadi selesai
            $booking->status = 'selesai';
            $booking->save();

            DB::commit();
            return redirect()->route('kasir.pembayaran.index')
                ->with('success', 'Pembayaran berhasil dikonfirmasi. Booking '
                    . $booking->kode_booking . ' telah selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
