<?php

namespace App\Http\Controllers\KasirServis;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use App\Models\RincianServis;
use App\Models\ProdukServis;
use App\Models\PembayaranServis;
use App\Models\User;
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
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemesan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
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
        $booking = BookingServis::with(['pelanggan', 'rincianServis.produkServis', 'layananServis', 'teknisi'])
            ->findOrFail($id);
            
        // Ambil list teknisi yang sesuai dengan tipe kendaraan
        $tipeKendaraan = strtolower($booking->layananServis->tipe_kendaraan ?? '');
        $roleDibutuhkan = $tipeKendaraan === 'mobil' ? 'Teknisi Mobil' : 'Teknisi Motor';

        $produk = ProdukServis::where('tipe_kendaraan', $tipeKendaraan)->get(); // Untuk pilihan sparepart sesuai kendaraan

        $listTeknisi = User::whereHas('roles', function ($q) use ($roleDibutuhkan) {
            $q->where('nama', $roleDibutuhkan);
        })->get();

        return view('kasirservis.booking.show', compact('booking', 'produk', 'listTeknisi'));
    }

    /**
     * Update status booking.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,batal',
        ]);

        $booking = BookingServis::findOrFail($id);

        if ($booking->status === 'batal') {
            return back()->with('error', 'Booking yang sudah batal tidak bisa diubah statusnya.');
        }

        $booking->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    /**
     * Assign teknisi ke booking.
     */
    public function assignTeknisi(Request $request, $id)
    {
        $request->validate([
            'teknisi_id' => 'required|exists:users,id',
        ]);

        $booking = BookingServis::findOrFail($id);

        if ($booking->status === 'batal') {
            return back()->with('error', 'Tidak bisa assign teknisi ke booking yang sudah batal.');
        }

        // Validasi role teknisi
        $teknisi = User::findOrFail($request->teknisi_id);
        if (!$teknisi->hasRole('Teknisi') && !$teknisi->hasRole('Teknisi Motor') && !$teknisi->hasRole('Teknisi Mobil')) {
            return back()->with('error', 'User yang dipilih bukan teknisi.');
        }

        $booking->update([
            'teknisi_id' => $request->teknisi_id,
        ]);

        return back()->with('success', 'Teknisi berhasil ditugaskan untuk booking ini.');
    }

    /**
     * Cetak Work Order (WO) untuk teknisi
     */
    public function printWo($id)
    {
        $booking = BookingServis::with(['pelanggan', 'layananServis'])
            ->findOrFail($id);

        if ($booking->status !== 'diproses') {
            return back()->with('error', 'Hanya booking dengan status "diproses" yang dapat dicetak Work Order-nya.');
        }

        return view('kasirservis.booking.print-wo', compact('booking'));
    }

    /**
     * Simpan atau update rincian servis berdasarkan input kasir
     */
    public function simpanRincian(Request $request, $id)
    {
        $booking = BookingServis::findOrFail($id);

        if (in_array($booking->status, ['selesai', 'batal'])) {
            return back()->with('error', 'Booking dengan status selesai atau batal tidak dapat diubah.');
        }

        $request->validate([
            'items'                    => 'required|array|min:1',
            'items.*.nama_item'        => 'required|string',
            'items.*.jumlah'           => 'required|integer|min:1',
            'items.*.harga_satuan'     => 'required|numeric|min:0',
            'foto_dokumentasi.*'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // 1. Hapus rincian lama
            $booking->rincianServis()->delete();

            // 2. Simpan rincian baru
            foreach ($request->items as $item) {
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $booking->rincianServis()->create([
                    'nama_item'        => $item['nama_item'],
                    'produk_servis_id' => $item['produk_servis_id'] ?? null,
                    'jumlah'           => $item['jumlah'],
                    'harga_satuan'     => $item['harga_satuan'],
                    'subtotal'         => $subtotal,
                ]);
            }

            // 3. Upload foto jika ada
            if ($request->hasFile('foto_dokumentasi')) {
                foreach ($request->file('foto_dokumentasi') as $foto) {
                    $path = $foto->store('foto_servis', 'public');
                    $booking->fotoServis()->create(['path_foto' => $path]);
                }
            }

            // 4. Update status ke diproses jika masih menunggu
            if ($booking->status === 'menunggu') {
                $booking->status = 'diproses';
                $booking->save();
            }

            // 5. Lanjut ke Pembayaran
            // Hitung total dari rincian yang baru disimpan
            $booking->load('rincianServis');
            $totalBiaya = $booking->rincianServis->sum('subtotal');

            // Generate kode pembayaran yang aman
            $kodePembayaran = 'PAY-' . strtoupper(substr($booking->kode_booking, 0, 6)) 
                              . '-' . now()->format('His');

            // Cek apakah sudah ada record pembayaran
            $existingKode = optional($booking->pembayaranServis)->kode_pembayaran;

            PembayaranServis::updateOrCreate(
                ['booking_servis_id' => $booking->id],
                [
                    'kode_pembayaran'   => $existingKode ?? $kodePembayaran,
                    'total_biaya'       => $totalBiaya,
                    'status_pembayaran' => 'belum_bayar',
                ]
            );

            // Update status booking ke siap_bayar
            $booking->status = 'siap_bayar';
            $booking->save();

            DB::commit();

            return redirect()->route('kasir.pembayaran.index')
                ->with('success', 'Rincian disimpan. Booking ' 
                    . $booking->kode_booking . ' siap diproses pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('simpanRincian error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
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
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemesan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
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
            'total_biaya'     => 'nullable|numeric|min:0', // Validasi input override
        ]);

        DB::beginTransaction();
        try {
            // Gunakan override dari kasir jika ada, jika tidak, hitung ulang dari rincian
            $totalBiaya = $request->filled('total_biaya') 
                ? $request->total_biaya 
                : $booking->rincianServis->sum('subtotal');

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
            return redirect()->route('kasir.laporan.index')
                ->with('success', 'Pembayaran berhasil dikonfirmasi. Booking '
                    . $booking->kode_booking . ' telah selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
