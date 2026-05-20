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

    public function create()
    {
        $layanans = \App\Models\LayananServis::where('is_active', true)->get();
        $mereks = \App\Models\MerekKendaraan::where('is_active', true)->orderBy('nama')->get();

        return view('kasirservis.booking.create', compact('layanans', 'mereks'));
    }

    public function getSlots(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
        ]);

        $tanggal    = $request->tanggal;
        $jamTersedia = $this->generateJamSlot();
        $sekarang   = \Carbon\Carbon::now('Asia/Jakarta');
        $isHariIni  = ($tanggal === $sekarang->toDateString());

        $bookingPerJam = BookingServis::where('tanggal_booking', $tanggal)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->selectRaw('jam_booking, COUNT(*) as total')
            ->groupBy('jam_booking')
            ->pluck('total', 'jam_booking')
            ->toArray();

        $slots = [];
        foreach ($jamTersedia as $jam) {
            $total          = $bookingPerJam[$jam] ?? 0;
            $kapasitasPenuh = $total >= 3;

            $sudahLewat = false;
            if ($isHariIni) {
                $jamInt     = (int) substr($jam, 0, 2);
                $sudahLewat = $sekarang->hour >= $jamInt;
            }

            $slots[] = [
                'jam'         => $jam,
                'terisi'      => (int) $total,
                'kapasitas'   => 3,
                'tersedia'    => !$kapasitasPenuh && !$sudahLewat,
                'sudah_lewat' => $sudahLewat,
            ];
        }

        $adaYangTersedia = collect($slots)->where('tersedia', true)->count() > 0;

        return response()->json([
            'slots'             => $slots,
            'ada_yang_tersedia' => $adaYangTersedia,
            'tanggal'           => $tanggal,
            'is_hari_ini'       => $isHariIni,
        ]);
    }

    public function getModelByMerek($merek_id)
    {
        $models = \App\Models\ModelKendaraan::where('merek_kendaraan_id', $merek_id)
            ->where('is_active', true)
            ->orderBy('nama_model')
            ->get(['id', 'nama_model']);

        return response()->json($models);
    }

    private function generateJamSlot(): array
    {
        $slots = [];
        for ($jam = 8; $jam <= 16; $jam++) {
            $slots[] = sprintf('%02d:00', $jam);
        }
        return $slots;
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'nama'               => 'required|string|max:255',
            'no_hp'              => 'required|string|max:20',
            'layanan_servis_id'  => 'required|exists:layanan_servis,id',
            'merek_kendaraan_id' => 'required|exists:merek_kendaraan,id',
            'model_kendaraan_id' => 'required|exists:model_kendaraan,id',
            'nomor_plat'         => 'required|string|max:20',
            'tahun_kendaraan'    => 'required|digits:4|integer|min:1990|max:' . date('Y'),
            'keluhan'            => 'nullable|string|max:500',
            'tanggal_booking'    => 'required|date|after_or_equal:today',
            'jam_booking'        => 'required|in:' . implode(',', $this->generateJamSlot()),
        ]);

        $modelExists = \App\Models\ModelKendaraan::where('id', $request->model_kendaraan_id)
            ->where('merek_kendaraan_id', $request->merek_kendaraan_id)
            ->exists();
        if (!$modelExists) {
            return back()->withErrors(['model_kendaraan_id' => 'Model kendaraan tidak sesuai dengan merek yang dipilih.'])->withInput();
        }

        $bookingAktif = BookingServis::where('no_hp', $request->no_hp)
            ->whereIn('status', ['menunggu', 'diproses', 'siap_bayar'])
            ->first();

        if ($bookingAktif) {
            return back()->withErrors([
                'no_hp' => 'Pelanggan masih memiliki booking servis yang sedang diproses (Kode: ' . $bookingAktif->kode_booking . ', Status: ' . strtoupper($bookingAktif->status) . '). Selesaikan booking tersebut terlebih dahulu.',
            ])->withInput();
        }

        $waktuBooking = \Carbon\Carbon::parse($request->tanggal_booking . ' ' . $request->jam_booking);
        if ($waktuBooking->isPast()) {
            return back()->withErrors(['jam_booking' => 'Waktu yang dipilih sudah lewat.'])->withInput();
        }

        if (!BookingServis::isSlotAvailable($request->tanggal_booking, $request->jam_booking)) {
            return back()->withErrors(['jam_booking' => 'Slot pada jam ini sudah penuh (Maks. 3).'])->withInput();
        }

        $layanan = \App\Models\LayananServis::findOrFail($request->layanan_servis_id);

        $user = User::where('no_hp', $request->no_hp)->first();
        if (!$user) {
            $user = User::create([
                'name'     => $request->nama,
                'username' => $request->nama,
                'no_hp'    => $request->no_hp,
                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
            ]);

            $role = \App\Models\Role::where('nama', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        do {
            $accessToken = \Illuminate\Support\Str::random(64);
        } while (BookingServis::where('access_token', $accessToken)->exists());

        $kodeBooking = strtoupper(substr($layanan->tipe_kendaraan ?? 'SRV', 0, 3)) . '-' . strtoupper(\Illuminate\Support\Str::random(6));

        $merek = \App\Models\MerekKendaraan::findOrFail($request->merek_kendaraan_id);
        $model = \App\Models\ModelKendaraan::findOrFail($request->model_kendaraan_id);

        $booking = BookingServis::create([
            'kode_booking'      => $kodeBooking,
            'user_id'           => $user->id,
            'nama_pemesan'      => $request->nama,
            'no_hp'             => $request->no_hp,
            'layanan_servis_id' => $request->layanan_servis_id,
            'merek_kendaraan'   => $merek->nama . ' ' . $model->nama_model,
            'nomor_plat'        => strtoupper($request->nomor_plat),
            'tahun_kendaraan'   => $request->tahun_kendaraan,
            'keluhan'           => $request->keluhan,
            'tanggal_booking'   => $request->tanggal_booking,
            'jam_booking'       => $request->jam_booking,
            'status'            => 'menunggu',
            'access_token'      => $accessToken,
        ]);

        // Kirim WhatsApp
        try {
            $linkAkses = route('user.servis.token.show', $accessToken);
            $fonnteToken = config('services.fonnte.token');

            if ($fonnteToken) {
                $tanggalFormat = \Carbon\Carbon::parse($request->tanggal_booking)->translatedFormat('d F Y');

                $pesanWa = "Yth. Bapak/Ibu {$request->nama},\n\n"
                    . "Terima kasih telah menggunakan layanan Sistem Servis Kendaraan di BLUD SMKN 1 Cirebon. Booking servis Anda telah berhasil dicatat dengan rincian sebagai berikut:\n\n"
                    . "Kode Booking: *{$kodeBooking}*\n"
                    . "Kendaraan: {$merek->nama} {$model->nama_model} ({$request->tahun_kendaraan})\n"
                    . "Layanan: {$layanan->nama_layanan}\n"
                    . "Jadwal: {$tanggalFormat} pukul {$request->jam_booking} WIB\n\n"
                    . "Untuk memantau status pengerjaan kendaraan dan detail riwayat servis Anda, silakan akses tautan resmi berikut:\n"
                    . "{$linkAkses}\n\n"
                    . "Harap simpan tautan di atas dengan baik. Tautan tersebut bersifat rahasia dan merupakan kunci akses Anda ke dalam sistem kami.\n\n"
                    . "Hormat kami,\n"
                    . "*Sistem Servis - BLUD SMKN 1 Cirebon*";

                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $fonnteToken,
                ])->post('https://api.fonnte.com/send', [
                    'target'      => $request->no_hp,
                    'message'     => $pesanWa,
                    'countryCode' => '62',
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Fonnte send error in kasir store: ' . $e->getMessage());
        }

        return redirect()->route('kasir.booking.index')->with('success', 'Booking berhasil dibuat oleh Kasir.');
    }
}
