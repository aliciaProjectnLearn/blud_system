<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\BookingAc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    // ── Daftar Booking ──────────────────────────────────
    public function index(Request $request)
    {
        $status = $request->status;

        $bookings = BookingAc::with(['user', 'layanan', 'teknisi'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $statusList = ['menunggu', 'proses', 'selesai'];

        $teknisiTersedia = User::whereHas('roles', fn($q) => $q->where('nama', 'Teknisi'))
        ->whereNotIn('id', function ($q) {
            $q->select('teknisi_id')
                ->from('booking_ac')
                ->where('status', 'proses')
                ->whereNotNull('teknisi_id');
        })
        ->get();

        return view('adminac.booking.index', compact('bookings', 'status', 'statusList', 'teknisiTersedia'));
    }

    // ── Detail Booking ──────────────────────────────────
    public function show($id)
    {
        $booking = BookingAc::with(['user', 'layanan', 'teknisi'])->findOrFail($id);

        $teknisiTersedia = User::whereHas('roles', fn($q) => $q->where('nama', 'Teknisi'))
            ->whereNotIn('id', function ($q) {
                $q->select('teknisi_id')
                    ->from('booking_ac')
                    ->where('status', 'proses')
                    ->whereNotNull('teknisi_id');
            })
            ->get();

        return view('adminac.booking.show', compact('booking', 'teknisiTersedia'));
    }

    // ── Approve Booking (menunggu → proses) ─────────────
    public function approve(Request $request, $id)
    {
        $request->validate([
            'teknisi_id' => 'required|exists:users,id',
        ]);

        $booking = BookingAc::findOrFail($id);

        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking tidak dapat di-approve.');
        }

        // Cek apakah teknisi sedang sibuk
        $sedangSibuk = DB::table('booking_ac')
            ->where('teknisi_id', $request->teknisi_id)
            ->where('status', 'proses')
            ->exists();

        if ($sedangSibuk) {
            return back()->with('error', 'Teknisi sedang sibuk, pilih teknisi lain.');
        }

        $booking->update([
            'teknisi_id' => $request->teknisi_id,
            'status'     => 'proses',
        ]);

        // ── 🚀 Integrasi Pengiriman Pesan WhatsApp via API (Contoh Menggunakan Fonnte) ──
        try {
            $teknisi = User::find($request->teknisi_id);
            // Muat relasi jika belum, agar data di pesan dinamis
            $booking->load(['user', 'layanan']);

            if ($teknisi && $teknisi->no_hp) {
                $noHpTeknisi = $teknisi->no_hp;
                
                // Format Pesan Dinamis dari Database
                $pesan = "*TUGAS SERVIS AC BARU!* 🛠️❄️\n\n";
                $pesan .= "Halo teknisi *{$teknisi->name}*, Anda memiliki pekerjaan baru dengan rincian:\n\n";
                $pesan .= "👤 *Nama Pelanggan*: " . ($booking->nama_pelanggan ?? optional($booking->user)->nama_lengkap ?? 'Guest') . "\n";
                $pesan .= "📞 *No. HP Pelanggan*: " . ($booking->no_hp ?? optional($booking->user)->no_hp ?? '-') . "\n";
                $pesan .= "🔧 *Layanan AC*: " . ($booking->layanan->nama ?? '-') . "\n";
                $pesan .= "🏷️ *Merek AC*: " . ($booking->merek_ac ?? '-') . "\n";
                $pesan .= "📅 *Tgl Kunjungan*: " . \Carbon\Carbon::parse($booking->tgl_kunjungan)->translatedFormat('d F Y') . "\n";
                $pesan .= "📍 *Alamat Lokasi*: {$booking->alamat}\n";
                $pesan .= "📝 *Keluhan*: " . ($booking->detail_keluhan ?? 'Tidak ada catatan') . "\n\n";
                $pesan .= "Silakan cek halaman Dashboard Anda untuk melakukan konfirmasi penyelesaian. Semangat bekerja!";

                // Mengirim ke endpoint API WhatsApp (Contoh Fonnte)
                // Ganti YOUR_API_TOKEN di file .env dengan token asli (misal: FONNTE_TOKEN=xxx)
                $apiToken = env('FONNTE_TOKEN', 'YOUR_API_TOKEN_HERE'); 

                if ($apiToken !== 'YOUR_API_TOKEN_HERE') {
                    $response = Http::withHeaders([
                        'Authorization' => $apiToken,
                    ])->post('https://api.fonnte.com/send', [
                        'target' => $noHpTeknisi,
                        'message' => $pesan,
                        'countryCode' => '62', // Otomatis mengonversi 08 menjadi +628
                    ]);

                    // Mencatat log respon berhasil/tidaknya API
                    Log::info('Notifikasi WA Teknisi: ' . $response->body());
                } else {
                    Log::warning('Token Fonnte belum diatur di .env. Pesan WA urung dikirim.');
                }
            }
        } catch (\Exception $e) {
            // Kita tidak ingin sistem error/gagal cuma karena koneksi ke WA error
            Log::error('Gagal mengirim WA ke teknisi: ' . $e->getMessage());
        }

        return back()->with('success', 'Booking berhasil di-approve dan teknisi ditugaskan (Notifikasi WA terkirim/diproses).');
    }

    // ── Selesai ─────────────────────────────────────────
    public function selesai($id)
    {
        $booking = BookingAc::findOrFail($id);

        if ($booking->status !== 'proses') {
            return back()->with('error', 'Booking harus berstatus proses untuk diselesaikan.');
        }

        $booking->update(['status' => 'selesai']);

        return back()->with('success', 'Booking berhasil diselesaikan.');
    }
}
