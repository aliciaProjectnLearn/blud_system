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

    // ── Fitur Booking Self-Service ──────────────────────
    public function pilihUnit()
    {
        $rukoList = Ruko::where('status_unit', 'kosong')->with('kategori', 'dokumentasiUnit')->get();
        return view('user.kantin.katalog', compact('rukoList'));
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
                // Update User: Selalu perbarui kolom nik dan alamat di tabel users
                $user->update([
                    'nik' => $request->nik,
                    'alamat' => $request->alamat,
                ]);

                // Buat data di tabel penyewa (tanpa NIK, karena sudah di tabel users)
                $penyewa = Penyewa::create([
                    'user_id' => $user->id,
                    'nama_usaha' => $request->nama_usaha,
                    'alamat' => $request->alamat,
                ]);
            }

            // 2. Buat record Booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'status' => 'menunggu', // Status default booking
            ]);

            // 3. Buat record SewaRuko (Pending)
            $tglMulai = now()->format('Y-m-d');
            $tglSelesai = now()->addYear()->format('Y-m-d'); // Otomatis 1 tahun sesuai requirment

            $sewaRuko = SewaRuko::create([
                'booking_id' => $booking->id,
                'penyewa_id' => $penyewa->id,
                'ruko_id' => $ruko->id,
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
                'harga_sewa_tahunan' => $ruko->harga,
                'status' => 'pending',
            ]);

            // 4. Update status ruko agar tidak dobel dipesan
            $ruko->update(['status_unit' => 'terisi']);

            // 5. Upload Dokumen Penyewaan
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

            return redirect()->route('user.kantin.dashboard')->with('success', 'Pengajuan sewa berhasil dikirim! Tunggu Admin memverifikasi dan menyetujui pengajuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
