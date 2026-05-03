<?php

namespace App\Http\Controllers\KasirFutsal;

use App\Http\Controllers\Controller;
use App\Models\PembayaranFutsal;
use App\Models\BookingFutsal;
use App\Models\TipePembayaran;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranKasirFutsalController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranFutsal::query();

        if ($request->filled('status')) {
            if ($request->status == 'lunas') {
                $query->where('status', PembayaranFutsal::STATUS_VERIFIKASI);
            } elseif ($request->status == 'belum lunas') {
                $query->where('status', PembayaranFutsal::STATUS_MENUNGGU);
            } elseif ($request->status == 'batal') {
                $query->where('status', PembayaranFutsal::STATUS_DIBATALKAN);
            }
        }

        $pembayarans = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        // Load manual info booking karena tidak ada relasi langsung di model
        foreach ($pembayarans as $pembayaran) {
            $pembayaran->bookingFutsal = BookingFutsal::with(['booking.user', 'lapangan'])
                ->where('booking_id', $pembayaran->booking_id)
                ->first();
            // Load booking.user sebagai fallback untuk transaksi reguler
            $pembayaran->load('tipePembayaran', 'booking.user');
            // Untuk transaksi membership: load data membership->user
            if ($pembayaran->jenis_transaksi === 'membership') {
                $pembayaran->membershipUser = \App\Models\Membership::with('user')
                    ->where('transaksi_id', $pembayaran->id)
                    ->first();
            }
        }

        return view('kasirfutsal.pembayaran.index', compact('pembayarans'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranFutsal::with(['tipePembayaran', 'booking.user'])->findOrFail($id);
        
        // Untuk transaksi membership, booking_id = null → tidak ada BookingFutsal
        $booking = null;
        if ($pembayaran->booking_id) {
            $booking = BookingFutsal::with(['booking.user', 'lapangan'])
                ->where('booking_id', $pembayaran->booking_id)
                ->first();
        }

        // Untuk transaksi membership, ambil data dari Membership
        $membership = null;
        if ($pembayaran->jenis_transaksi === 'membership') {
            $membership = \App\Models\Membership::with(['paket', 'user'])
                ->where('transaksi_id', $pembayaran->id)
                ->first();
        }

        $tipePembayaran = TipePembayaran::all();

        // Tentukan metode pembayaran dari data yang sudah dipilih user saat booking
        $metodeDariBooking = null;
        $namaMetode = null;
        if ($pembayaran->tipe_pembayaran_id) {
            $tipeExisting = TipePembayaran::find($pembayaran->tipe_pembayaran_id);
            $metodeDariBooking = $pembayaran->tipe_pembayaran_id;
            $namaMetode = $tipeExisting->nama ?? null;
        }

        // Jenis booking menentukan opsi metode yang tersedia
        $jenisBooking = $booking->jenis_pembayaran ?? ($pembayaran->jenis_transaksi === 'membership' ? 'membership' : 'reguler');

        return view('kasirfutsal.pembayaran.show', compact(
            'pembayaran', 'booking', 'tipePembayaran',
            'metodeDariBooking', 'namaMetode', 'jenisBooking', 'membership'
        ));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $pembayaran = PembayaranFutsal::findOrFail($id);

        if ($pembayaran->status === PembayaranFutsal::STATUS_VERIFIKASI) {
            return back()->with('error', 'Pembayaran sudah lunas.');
        }

        DB::beginTransaction();
        try {
            // Gunakan tipe dari request, atau fallback ke tipe yang sudah ada di DB
            $tipeId = $request->tipe_pembayaran_id ?? $pembayaran->tipe_pembayaran_id;
            if (!$tipeId) {
                return back()->with('error', 'Metode pembayaran tidak ditemukan. Harap pilih metode terlebih dahulu.');
            }

            $pembayaran->status = PembayaranFutsal::STATUS_VERIFIKASI;
            $pembayaran->tipe_pembayaran_id = $tipeId;
            $pembayaran->tgl_bayar = now();
            $pembayaran->save();
            
            // Update status booking sesuai alur: menunggu→dikonfirmasi, dikonfirmasi→selesai
            $booking = BookingFutsal::with(['booking.user', 'lapangan'])->where('booking_id', $pembayaran->booking_id)->first();
            
            if ($pembayaran->jenis_transaksi === 'membership') {
                // AKTIVASI MEMBERSHIP
                $membership = \App\Models\Membership::where('transaksi_id', $pembayaran->id)->first();
                if ($membership) {
                    $membership->status = 'aktif';
                    $membership->save();
                }
            } elseif ($booking) {
                // PROSES BOOKING REGULER/EVENT
                if ($booking->status === 'menunggu') {
                    // Kasir proses bayar = otomatis konfirmasi booking
                    $booking->status = 'dikonfirmasi';
                } elseif ($booking->status === 'dikonfirmasi') {
                    // Sudah dikonfirmasi sebelumnya → tandai selesai
                    $booking->status = 'selesai';
                }
                $booking->save();
            }

            // Kirim notifikasi WA
            if ($pembayaran->jenis_transaksi === 'membership') {
                // Membership: ambil no_hp dari user yang terasosiasi dengan membership
                $membershipForWa = \App\Models\Membership::with(['paket', 'user'])->where('transaksi_id', $pembayaran->id)->first();
                $noHp = $membershipForWa->user->no_hp ?? null;
            } else {
                $noHp = $booking->no_hp ?? null;
            }

            if ($noHp) {
                try {
                    $apiToken = env('FONNTE_TOKEN');
                    
                    if ($pembayaran->jenis_transaksi === 'membership') {
                        // PESAN WA UNTUK MEMBERSHIP (gunakan $membershipForWa yang sudah diload)
                        $namaPemesan = $membershipForWa->user->nama_lengkap ?? ($membershipForWa->user->name ?? 'Pelanggan');
                        $namaPaket = $membershipForWa->paket->nama_paket ?? 'Paket Futsal';
                        $kuota = $membershipForWa->paket->jumlah_kuota ?? 0;
                        $harga = number_format($pembayaran->jumlah_bayar, 0, ',', '.');

                        $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
                        $pesan .= "Pembayaran paket membership futsal Anda telah *Diverifikasi* ✅\n\n";
                        $pesan .= "📋 *Detail Membership (AKTIF):*\n";
                        $pesan .= "📦 Paket   : *{$namaPaket}*\n";
                        $pesan .= "⏱️ Kuota   : *{$kuota} Jam*\n";
                        $pesan .= "💰 Harga   : *Rp {$harga}*\n\n";
                        $pesan .= "Anda sekarang dapat menggunakan kuota membership ini untuk melakukan booking lapangan melalui portal user.\n\n";
                        $pesan .= "Terima kasih 🙏\n— Admin Futsal BLUD SMK";
                    } else {
                        // PESAN WA UNTUK BOOKING REGULER/EVENT
                        $namaPemesan = $booking->nama_pemesan ?? ($booking->user->name ?? 'Pelanggan');
                        $lapangan    = $booking->lapangan->nama ?? '-';
                        $jamMulai    = \Carbon\Carbon::parse($booking->start_datetime)->format('H:i');
                        $jamSelesai  = \Carbon\Carbon::parse($booking->end_datetime)->format('H:i');
                        $tglMain     = \Carbon\Carbon::parse($booking->start_datetime)->translatedFormat('l, d F Y');
                        $tokenLink   = url('/user/access/' . $booking->access_token);

                        $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
                        $pesan .= "Pembayaran booking futsal Anda telah *lunas* ✅\n\n";
                        $pesan .= "📋 *Detail Booking:*\n";
                        $pesan .= "🏟️ Lapangan: *{$lapangan}*\n";
                        $pesan .= "📅 Tanggal : *{$tglMain}*\n";
                        $pesan .= "⏰ Waktu   : *{$jamMulai} - {$jamSelesai}*\n";
                        $pesan .= "💰 Pembayaran: *Lunas*\n\n";
                        $pesan .= "🔗 Lihat detail booking Anda di:\n{$tokenLink}\n\n";
                        $pesan .= "Terima kasih telah berolahraga di BLUD Futsal! 🙏";
                    }

                    // Normalisasi no_hp: strip semua karakter non-digit
                    $noHpFormatted = preg_replace('/[^0-9]/', '', $noHp);

                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => $apiToken,
                    ])->post('https://api.fonnte.com/send', [
                        'target'      => $noHpFormatted,
                        'message'     => $pesan,
                        'countryCode' => '62',
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Gagal kirim WA lunas kasir futsal: ' . $e->getMessage());
                }
            }

            LogActivity::create([
                'user_id'             => auth()->id(),
                'nama_user'           => auth()->user()->name,
                'sistem'              => 'Futsal',
                'aktivitas'           => $pembayaran->jenis_transaksi === 'membership' ? 'Aktivasi Membership' : 'Proses Pembayaran',
                'deskripsi_aktivitas' => $pembayaran->jenis_transaksi === 'membership'
                    ? 'Kasir Futsal mengaktifkan membership ID: ' . $pembayaran->id
                    : 'Kasir Futsal memproses pembayaran ID: ' . $pembayaran->id,
            ]);

            DB::commit();
            $successMsg = $pembayaran->jenis_transaksi === 'membership'
                ? 'Paket membership berhasil diverifikasi dan diaktifkan.'
                : 'Pembayaran berhasil diproses menjadi lunas.';
            return redirect()->route('kasirfutsal.pembayaran.index')->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function tolakPembayaran(Request $request, $id)
    {
        $pembayaran = PembayaranFutsal::findOrFail($id);

        if ($pembayaran->status !== PembayaranFutsal::STATUS_MENUNGGU) {
            return back()->with('error', 'Hanya pembayaran dengan status "Menunggu" yang dapat ditolak.');
        }

        $request->validate([
            'alasan_tolak' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $pembayaran->status = PembayaranFutsal::STATUS_DIBATALKAN;
            $pembayaran->tgl_bayar = now(); // Catat waktu penolakan
            $pembayaran->save();

            // Jika membership → set status membership menjadi 'ditolak'
            if ($pembayaran->jenis_transaksi === 'membership') {
                $membership = \App\Models\Membership::where('transaksi_id', $pembayaran->id)->first();
                if ($membership) {
                    $membership->status = 'ditolak';
                    $membership->save();
                }
                $membershipForWa = \App\Models\Membership::with(['paket', 'user'])->where('transaksi_id', $pembayaran->id)->first();
                $noHp = $membershipForWa->user->no_hp ?? null;
            } else {
                // Booking reguler/event → kembalikan status booking ke 'dibatalkan'
                $booking = BookingFutsal::with(['booking'])->where('booking_id', $pembayaran->booking_id)->first();
                if ($booking) {
                    $booking->status = 'dibatalkan';
                    $booking->save();
                    if ($booking->booking) {
                        $booking->booking->status = 'dibatalkan';
                        $booking->booking->save();
                    }
                }
                $noHp = $booking->no_hp ?? null;
                $membershipForWa = null;
            }

            // Kirim notifikasi WA penolakan
            if ($noHp) {
                try {
                    $apiToken = env('FONNTE_TOKEN');
                    $alasan = $request->alasan_tolak ?: 'Bukti pembayaran tidak valid atau tidak sesuai.';
                    $noHpFormatted = preg_replace('/[^0-9]/', '', $noHp);

                    if ($pembayaran->jenis_transaksi === 'membership') {
                        $namaPemesan = $membershipForWa->user->nama_lengkap ?? ($membershipForWa->user->name ?? 'Pelanggan');
                        $namaPaket = $membershipForWa->paket->nama_paket ?? 'Paket Futsal';

                        $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
                        $pesan .= "Mohon maaf, pembelian paket membership futsal Anda *Ditolak* ❌\n\n";
                        $pesan .= "📦 Paket   : *{$namaPaket}*\n";
                        $pesan .= "❗ Alasan  : *{$alasan}*\n\n";
                        $pesan .= "Silahkan hubungi admin atau coba lakukan pembelian ulang dengan bukti pembayaran yang benar.\n\n";
                        $pesan .= "Terima kasih 🙏\n— Admin Futsal BLUD SMK";
                    } else {
                        $namaPemesan = $booking->nama_pemesan ?? 'Pelanggan';

                        $pesan  = "Halo *{$namaPemesan}* 👋\n\n";
                        $pesan .= "Mohon maaf, pembayaran booking futsal Anda *Ditolak* ❌\n\n";
                        $pesan .= "❗ Alasan  : *{$alasan}*\n\n";
                        $pesan .= "Silahkan hubungi admin atau lakukan booking ulang.\n\n";
                        $pesan .= "Terima kasih 🙏\n— Admin Futsal BLUD SMK";
                    }

                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => $apiToken,
                    ])->post('https://api.fonnte.com/send', [
                        'target'      => $noHpFormatted,
                        'message'     => $pesan,
                        'countryCode' => '62',
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Gagal kirim WA penolakan kasir futsal: ' . $e->getMessage());
                }
            }

            LogActivity::create([
                'user_id'             => auth()->id(),
                'nama_user'           => auth()->user()->name,
                'sistem'              => 'Futsal',
                'aktivitas'           => 'Tolak Pembayaran',
                'deskripsi_aktivitas' => 'Kasir Futsal menolak pembayaran ID: ' . $pembayaran->id,
            ]);

            DB::commit();
            return redirect()->route('kasirfutsal.pembayaran.index')
                ->with('success', 'Pembayaran berhasil ditolak dan pemesan telah dinotifikasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }
}
