<?php

namespace App\Http\Controllers;

use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SewaTokenController extends Controller
{
    public function show($token)
    {
        $sewa = SewaRuko::where('access_token', $token)
            ->with(['ruko', 'pembayaran' => fn($q) => $q->orderBy('termin_ke'), 'dokumen'])
            ->firstOrFail();

        // Riwayat sewa lain dengan nomor HP yang sama
        $riwayat = SewaRuko::where('no_hp_snapshot', $sewa->no_hp_snapshot)
            ->where('id', '!=', $sewa->id)
            ->with('ruko')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.kantin.sewa-token', compact('sewa', 'riwayat'));
    }

    public function uploadBukti(Request $request, $token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        
        $request->validate([
            'pembayaran_id'   => 'required|exists:pembayaran_ruko,id',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pembayaran = PembayaranRuko::where('id', $request->pembayaran_id)
            ->where('sewa_ruko_id', $sewa->id)
            ->firstOrFail();

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            
            $pembayaran->update([
                'bukti_pembayaran'   => $path,
                'path_bukti'         => $path, // for compatibility with old system
                'tanggal_bayar'      => now(),
                'status_pembayaran'  => 'pending', // remains pending until admin approves
                'status'             => 'verifikasi', // for compatibility
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah. Mohon tunggu verifikasi admin.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
