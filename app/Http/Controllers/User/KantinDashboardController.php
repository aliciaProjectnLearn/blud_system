<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use App\Models\Ruko;
use App\Models\Booking;
use App\Models\Penyewa;
use App\Models\DokumenPenyewaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class KantinDashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $penyewa = $user->penyewa;

        if (!$penyewa) {
            return view('user.kantin.dashboard', [
                'penyewa'          => null,
                'sewaAktif'        => null,
                'totalSewa'        => 0,
                'totalDikonfirmasi' => 0,
                'totalSelesai'     => 0,
                'tagihanMendatang' => null,
                'transaksiTerbaru' => collect(),
                'dataGrafik'       => [],
                'labelGrafik'      => [],
            ]);
        }

        $penyewaId = $penyewa->getKey();

        // ── Sewa aktif ──────────────────────────────────────
        $sewaAktif = SewaRuko::where('penyewa_id', $penyewaId)
            ->whereIn('status', ['aktif', 'pending'])
            ->with(['ruko', 'pembayaran' => fn($q) => $q->orderBy('tgl_jatuh_tempo')])
            ->orderByRaw("FIELD(status, 'pending', 'aktif') ASC")
            ->latest()
            ->first();

        // ── Summary cards ───────────────────────────────────
        $totalSewa = SewaRuko::where('penyewa_id', $penyewaId)->count();

        $totalDikonfirmasi = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->where('status', 'verifikasi')
            ->count();

        $totalSelesai = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->where('status', 'lunas')
            ->count();

        // ── Tagihan mendatang (status menunggu, paling dekat jatuh tempo) ──
        $tagihanMendatang = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->where('status', 'menunggu')
            ->whereNotNull('tgl_jatuh_tempo')
            ->orderBy('tgl_jatuh_tempo')
            ->with('sewaRuko.ruko')
            ->first();

        // ── Transaksi terbaru (5 data) ──────────────────────
        $transaksiTerbaru = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->with('sewaRuko.ruko')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ── Grafik tagihan per bulan (tahun ini) ────────────
        $grafik = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(jumlah_tagihan) as total')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('bulan')
            ->get();

        $labelGrafik  = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $dataGrafik   = array_fill(0, 12, 0);
        foreach ($grafik as $g) {
            $dataGrafik[$g->bulan - 1] = $g->total;
        }

        return view('user.kantin.dashboard', compact(
            'penyewa',
            'sewaAktif',
            'totalSewa',
            'totalDikonfirmasi',
            'totalSelesai',
            'tagihanMendatang',
            'transaksiTerbaru',
            'dataGrafik',
            'labelGrafik'
        ));
    }

    public function riwayat()
    {
        $user    = Auth::user();
        $penyewa = $user->penyewa;

        $riwayat = $penyewa
            ? PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewa->getKey()))
            ->with('sewaRuko.ruko')
            ->where('status', 'lunas')
            ->orderBy('created_at', 'desc')
            ->get()
            : collect();

        $pengajuan = $penyewa
            ? SewaRuko::where('penyewa_id', $penyewa->getKey())
                ->with(['ruko.kategori', 'dokumen'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('user.kantin.riwayat', compact('riwayat', 'pengajuan'));
    }

    public function tagihan()
    {
        $user    = Auth::user();
        $penyewa = $user->penyewa;

        $tagihan = $penyewa
            ? PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewa->getKey()))
                ->with('sewaRuko.ruko')
                ->whereIn('status', ['menunggu', 'verifikasi'])
                ->orderBy('tgl_jatuh_tempo', 'asc')
                ->get()
            : collect();

        return view('user.kantin.tagihan', compact('tagihan'));
    }

    // ── Fitur Booking Self-Service ──────────────────────
    public function pilihUnit()
    {
        $rukoList = Ruko::whereHas('kategori', function($q) {
                $q->where('tipe', 'kantin');
            })
            ->with(['kategori', 'dokumentasiUnit'])
            ->orderBy('kategori_id')
            ->orderBy('kode_unit')
            ->get();

        $kategoriList = \App\Models\Kategori::where('tipe', 'kantin')->get();

        $rukoDataJs = $rukoList->map(function($r) {
            return [
                'id'          => $r->id,
                'kode_unit'   => $r->kode_unit,
                'status_unit' => $r->status_unit,
                'kategori_id' => $r->kategori_id,
                'harga'       => $r->harga,
            ];
        })->values();

        return view('user.kantin.katalog', compact('rukoList', 'kategoriList', 'rukoDataJs'));
    }

    public function formSewa($id)
    {
        $ruko = Ruko::with('kategori', 'dokumentasiUnit')->findOrFail($id);

        // Pastikan ruko masih kosong
        if ($ruko->status_unit !== 'kosong') {
            return redirect()->route('user.kantin.katalog')->with('error', 'Unit sudah tidak tersedia.');
        }

        $user = Auth::user();
        $penyewa = $user->penyewa;

        return view('user.kantin.booking', compact('ruko', 'user', 'penyewa'));
    }

    public function storeSewa(Request $request, $id)
    {
        $ruko = Ruko::findOrFail($id);

        if ($ruko->status_unit !== 'kosong') {
            return redirect()->route('user.kantin.katalog')->with('error', 'Unit sudah tidak tersedia.');
        }

        $user = Auth::user();

        // Validasi input
        $rules = [
            'tgl_mulai'   => 'required|date|after_or_equal:today',
            'dokumen_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        // Jika user belum punya profil penyewa, wajib isi nama usaha dan nik
        if (!$user->penyewa) {
            $rules['nama_usaha'] = 'required|string|max:255';
            $rules['nik'] = 'required|string|size:16';
            $rules['alamat'] = 'required|string';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            // 1. Dapatkan atau Buat Penyewa
            $penyewa = $user->penyewa;
            if (!$penyewa) {
                // Update User
                $user->update([
                    'nik' => $request->nik,
                    'alamat' => $request->alamat,
                ]);

                // Buat data di tabel penyewa
                $penyewa = Penyewa::create([
                    'user_id' => $user->id,
                    'nama_usaha' => $request->nama_usaha,
                    'alamat' => $request->alamat,
                ]);
            }

            // 2. Buat record Booking (optional, but keep for compatibility if needed elsewhere)
            $booking = Booking::create([
                'user_id' => $user->id,
                'status' => 'menunggu',
            ]);

            // 3. Hitung tanggal mulai & selesai
            $tglMulai = \Carbon\Carbon::parse($request->tgl_mulai);
            $tglSelesai = $tglMulai->copy()->addYear();

            // 4. Buat record SewaRuko (Pending)
            $sewaRuko = SewaRuko::create([
                'booking_id' => $booking->id,
                'penyewa_id' => $penyewa->id,
                'ruko_id' => $ruko->id,
                'tgl_mulai' => $tglMulai->format('Y-m-d'),
                'tgl_selesai' => $tglSelesai->format('Y-m-d'),
                'harga_sewa_tahunan' => $ruko->harga,
                'status' => 'pending',
            ]);

            // 5. Generate Pembayaran Termin
            $jumlahPerTermin = intdiv($ruko->harga, 2);
            
            $tipePembayaranId = 1;
            if ($request->has('metode_pembayaran')) {
                $tipePembayaranId = \App\Models\TipePembayaran::where('nama', 'like', '%' . $request->metode_pembayaran . '%')
                    ->value('id') ?? 1;
            }
            
            // Termin 1: Jatuh tempo hari ini / saat tgl_mulai
            PembayaranRuko::create([
                'sewa_ruko_id'       => $sewaRuko->id,
                'booking_id'         => $booking->id,
                'tipe_pembayaran_id' => $tipePembayaranId, // Use the mapped ID
                'termin'             => 1,
                'tgl_jatuh_tempo'    => $tglMulai,
                'jumlah_tagihan'     => $jumlahPerTermin,
                'status'             => 'menunggu',
            ]);

            // Termin 2: Jatuh tempo 6 bulan setelah tgl_mulai
            PembayaranRuko::create([
                'sewa_ruko_id'       => $sewaRuko->id,
                'booking_id'         => $booking->id,
                'tipe_pembayaran_id' => $tipePembayaranId,
                'termin'             => 2,
                'tgl_jatuh_tempo'    => $tglMulai->copy()->addMonths(6),
                'jumlah_tagihan'     => $ruko->harga - $jumlahPerTermin,
                'status'             => 'menunggu',
            ]);

            // 6. Update status ruko agar tidak dobel dipesan (sementara)
            $ruko->update(['status_unit' => 'terisi']);

            // 7. Upload Dokumen Penyewaan
            if ($request->hasFile('dokumen_ktp')) {
                $path = $request->file('dokumen_ktp')->store('dokumen_penyewaan', 'public');
                $noMou = 'MOU-' . time() . '-' . $sewaRuko->id;

                DokumenPenyewaan::create([
                    'sewa_id' => $sewaRuko->id,
                    'no_mou' => $noMou,
                    'nama_dokumen' => 'KTP/Identitas Pengaju',
                    'path_file' => $path,
                ]);
            }

            DB::commit();

            return redirect()->route('user.kantin.tagihan')->with('success', 'Pengajuan sewa berhasil dikirim! Silakan lakukan pembayaran Termin 1 untuk mengaktifkan sewa.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    public function confirmPembayaran(Request $request, $id)
    {
        $pembayaran = PembayaranRuko::findOrFail($id);
        
        $request->validate([
            'tipe_pembayaran_id' => 'required|exists:tipe_pembayaran,id',
            'bukti_pembayaran'   => 'required_if:tipe_pembayaran_id,1,3|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'tipe_pembayaran_id' => $request->tipe_pembayaran_id,
            'status'             => 'verifikasi',
            'tgl_bayar'          => now(),
        ];

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran_ruko', 'public');
            $data['path_bukti'] = $path;
        }

        $pembayaran->update($data);

        return redirect()->route('user.kantin.tagihan')->with('success', 'Bukti pembayaran berhasil diunggah. Mohon tunggu verifikasi dari Admin.');
    }

    // ── Booking Langsung (Streamlined) ─────────────────────

    /**
     * Tampilkan form booking langsung (tanpa harus pilih dari halaman katalog).
     */
    public function showBookingForm()
    {
        $units = Ruko::whereHas('kategori', fn($q) => $q->where('tipe', 'kantin'))
            ->where('status_unit', 'kosong')
            ->with(['kategori', 'dokumentasiUnit'])
            ->orderByRaw("CAST(SUBSTRING(kode_unit, 4) AS UNSIGNED) ASC")
            ->get();

        $user    = Auth::user();
        $penyewa = $user->penyewa;

        return view('user.kantin.booking-form', compact('units', 'user', 'penyewa'));
    }

    /**
     * Endpoint AJAX — kembalikan detail unit dalam format JSON.
     */
    public function getUnitDetail($id)
    {
        $ruko = Ruko::with(['kategori', 'dokumentasiUnit'])
            ->where('status_unit', 'kosong')
            ->whereHas('kategori', fn($q) => $q->where('tipe', 'kantin'))
            ->find($id);

        if (!$ruko) {
            return response()->json(['error' => 'Unit tidak ditemukan atau sudah terisi.'], 404);
        }

        $dokumentasi = $ruko->dokumentasiUnit->map(fn($d) => [
            'id'            => $d->id,
            'file'          => $d->file,
            'tipe'          => $d->tipe,
            'judul_dokumen' => $d->judul_dokumen,
            'url'           => asset('storage/' . str_replace('\\', '/', $d->file)),
        ]);

        return response()->json([
            'id'        => $ruko->id,
            'kode_unit' => $ruko->kode_unit,
            'no_unit'   => $ruko->no_unit ?? null,
            'kategori'  => [
                'id'   => $ruko->kategori->id ?? null,
                'nama' => $ruko->kategori->nama ?? '-',
                'tipe' => $ruko->kategori->tipe ?? '-',
            ],
            'harga'       => $ruko->harga,
            'status_unit' => $ruko->status_unit,
            'dokumentasi' => $dokumentasi,
        ]);
    }

    /**
     * Simpan pengajuan sewa baru dari form booking langsung.
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'ruko_id'           => 'required|exists:ruko,id',
            'tgl_mulai'         => 'required|date|after_or_equal:today',
            'metode_pembayaran' => 'required|in:tunai,qris',
        ]);

        $ruko = Ruko::find($request->ruko_id);

        // Race condition guard
        if (!$ruko || $ruko->status_unit !== 'kosong') {
            return back()->with('error', 'Unit yang Anda pilih sudah tidak tersedia. Silakan pilih unit lain.');
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            // 1. Dapatkan atau buat record Penyewa secara otomatis
            $penyewa = $user->penyewa;
            if (!$penyewa) {
                $penyewa = Penyewa::create([
                    'user_id'    => $user->id,
                    'nama_usaha' => $user->name,
                    'alamat'     => $user->alamat ?? '-',
                    'nik'        => $user->nik    ?? null,
                ]);
            }

            // 2. Hitung tanggal
            $tglMulai   = Carbon::parse($request->tgl_mulai);
            $tglSelesai = $tglMulai->copy()->addYear();

            // 3. Buat Booking (parent record)
            $booking = Booking::create([
                'user_id' => $user->id,
                'status'  => 'menunggu',
            ]);

            // 4. Buat record SewaRuko
            $sewaRuko = SewaRuko::create([
                'booking_id'         => $booking->id,
                'penyewa_id'         => $penyewa->id,
                'ruko_id'            => $ruko->id,
                'tgl_mulai'          => $tglMulai->format('Y-m-d'),
                'tgl_selesai'        => $tglSelesai->format('Y-m-d'),
                'harga_sewa_tahunan' => $ruko->harga,
                'status'             => 'pending',
            ]);

            // 5. Generate 2 termin pembayaran
            $jumlahPerTermin = intdiv((int) $ruko->harga, 2);

            $tipePembayaranId = \App\Models\TipePembayaran::where('nama', 'like', '%' . $request->metode_pembayaran . '%')
                ->value('id') ?? 1;

            PembayaranRuko::create([
                'sewa_ruko_id'       => $sewaRuko->id,
                'booking_id'         => $booking->id,
                'tipe_pembayaran_id' => $tipePembayaranId,
                'termin'             => 1,
                'tgl_jatuh_tempo'    => $tglMulai->format('Y-m-d'),
                'jumlah_tagihan'     => $jumlahPerTermin,
                'status'             => 'menunggu',
            ]);

            PembayaranRuko::create([
                'sewa_ruko_id'       => $sewaRuko->id,
                'booking_id'         => $booking->id,
                'tipe_pembayaran_id' => $tipePembayaranId,
                'termin'             => 2,
                'tgl_jatuh_tempo'    => $tglMulai->copy()->addMonths(6)->format('Y-m-d'),
                'jumlah_tagihan'     => $ruko->harga - $jumlahPerTermin,
                'status'             => 'menunggu',
            ]);

            // 6. Tandai unit sebagai terisi
            $ruko->update(['status_unit' => 'terisi']);

            DB::commit();

            return redirect()->route('user.kantin.tagihan')
                ->with('success', 'Pengajuan sewa berhasil dikirim! Silakan lakukan pembayaran Termin 1 untuk mengaktifkan sewa Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function downloadKwitansi($id)
    {
        $pembayaran = PembayaranRuko::findOrFail($id);

        // Pastikan hanya pemilik yang bisa download
        $penyewa = Auth::user()->penyewa;
        if (!$penyewa || $pembayaran->sewaRuko->penyewa_id !== $penyewa->id) {
            abort(403);
        }

        if ($pembayaran->status !== 'lunas') {
            return back()->with('error', 'Kwitansi hanya tersedia untuk pembayaran yang sudah lunas.');
        }

        if (!$pembayaran->path_kwitansi || !\Storage::disk('public')->exists($pembayaran->path_kwitansi)) {
            return back()->with('error', 'File kwitansi belum tersedia. Hubungi admin.');
        }

        $namaFile = 'kwitansi-' . str_replace('/', '-', $pembayaran->no_kwitansi) . '.pdf';
        return response()->download(storage_path('app/public/' . $pembayaran->path_kwitansi), $namaFile);
    }
}
