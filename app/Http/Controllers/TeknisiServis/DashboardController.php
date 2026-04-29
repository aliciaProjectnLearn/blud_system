<?php

namespace App\Http\Controllers\TeknisiServis;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan daftar pekerjaan teknisi servis (motor/mobil).
     * Read-only — tidak ada aksi update status.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Tentukan role & tipe kendaraan berdasarkan role user
        $roleTeknisi = DB::table('roles_users')
            ->join('roles', 'roles_users.role_id', '=', 'roles.id')
            ->where('roles_users.user_id', $userId)
            ->value('roles.nama');

        $tipeKendaraan = match ($roleTeknisi) {
            'Teknisi Motor' => 'motor',
            'Teknisi Mobil' => 'mobil',
            default         => null,
        };

        // Filter dari request
        $status  = $request->get('status');
        $tanggal = $request->get('tanggal');

        // ── Pekerjaan Aktif ──────────────────────────────────
        $queryAktif = BookingServis::with(['user', 'layananServis'])
            ->where('teknisi_id', $userId)
            ->whereNotIn('status', ['selesai', 'batal']);

        if ($tipeKendaraan) {
            $queryAktif->whereHas('layananServis', function($q) use ($tipeKendaraan) {
                $q->where('tipe_kendaraan', $tipeKendaraan);
            });
        }
        if ($status) {
            $queryAktif->where('status', $status);
        }
        if ($tanggal) {
            $queryAktif->whereDate('tanggal_booking', $tanggal);
        }

        $pekerjaanAktif = $queryAktif->orderBy('tanggal_booking', 'asc')->paginate(10)->withQueryString();

        // ── Histori Pekerjaan Selesai ────────────────────────
        $queryHistori = BookingServis::with(['user', 'layananServis', 'pembayaranServis'])
            ->where('teknisi_id', $userId)
            ->where('status', 'selesai');

        if ($tipeKendaraan) {
            $queryHistori->whereHas('layananServis', function($q) use ($tipeKendaraan) {
                $q->where('tipe_kendaraan', $tipeKendaraan);
            });
        }
        if ($tanggal) {
            $queryHistori->whereDate('tanggal_booking', $tanggal);
        }

        $historiPekerjaan = $queryHistori->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // ── Statistik ────────────────────────────────────────
        $queryTotal = BookingServis::where('teknisi_id', $userId)
            ->where('status', 'selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year);

        if ($tipeKendaraan) {
            $queryTotal->whereHas('layananServis', function($q) use ($tipeKendaraan) {
                $q->where('tipe_kendaraan', $tipeKendaraan);
            });
        }

        $totalSelesaiBulanIni = $queryTotal->count();

        return view('teknisiservis.dashboard.index', compact(
            'pekerjaanAktif',
            'historiPekerjaan',
            'totalSelesaiBulanIni',
            'roleTeknisi',
            'tipeKendaraan'
        ));
    }

    /**
     * Menampilkan detail satu pekerjaan (read-only).
     */
    public function show($id)
    {
        $pekerjaan = BookingServis::with(['user', 'layananServis', 'rincianServis'])
            ->findOrFail($id);

        // Validasi 403: hanya teknisi yang di-assign yang boleh lihat
        if ($pekerjaan->teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pekerjaan ini.');
        }

        return view('teknisiservis.dashboard.show', compact('pekerjaan'));
    }
}
