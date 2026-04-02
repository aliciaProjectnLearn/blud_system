<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranRuko;
use App\Models\SewaRuko;
use App\Models\Penyewa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranRuko::with(['sewaRuko.penyewa', 'sewaRuko.ruko']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter termin
        if ($request->filled('termin')) {
            $query->where('termin', $request->termin);
        }

        // Search penyewa
        if ($request->filled('search')) {
            $query->whereHas('sewaRuko.penyewa', function ($q) use ($request) {
                $q->where('nama_usaha', 'like', '%' . $request->search . '%');
            });
        }

        $pembayarans = $query->latest()->paginate(15);

        // Daftar penyewa untuk filter
        $penyewas = Penyewa::all();

        return view('adminkantin.pembayaran.index', compact('pembayarans', 'penyewas'));
    }

    public function show(PembayaranRuko $pembayaran)
    {
        $pembayaran->load([
            'sewaRuko.penyewa.user',
            'sewaRuko.ruko.kategori',
            'sewaRuko.dokumen',
            'tipe',
        ]);
        return view('adminkantin.pembayaran.show', compact('pembayaran'));
    }

    public function update(Request $request, PembayaranRuko $pembayaran)
    {
        // Cegah pembayaran ganda
        if ($pembayaran->status === 'verifikasi') {
            return redirect()->back()->with('error', 'Pembayaran ini sudah terverifikasi.');
        }

        $request->validate([
            'tgl_bayar'         => 'required|date',
            'tipe_pembayaran_id' => 'required|exists:tipe_pembayaran,id',
        ]);

        // Generate no kwitansi otomatis
        $noKwitansi = PembayaranRuko::generateNoKwitansi();

        $pembayaran->update([
            'tgl_bayar'          => $request->tgl_bayar,
            'tipe_pembayaran_id' => $request->tipe_pembayaran_id,
            'status'             => 'verifikasi',
            'no_kwitansi'        => $noKwitansi,
        ]);

        return redirect()->route('adminkantin.pembayaran.show', $pembayaran)
            ->with('success', "Pembayaran berhasil dikonfirmasi. No. Kwitansi: {$noKwitansi}");
    }

    public function downloadKwitansi(PembayaranRuko $pembayaran)
    {
        if ($pembayaran->status !== 'verifikasi') {
            return redirect()->back()->with('error', 'Kwitansi hanya tersedia untuk pembayaran yang sudah terverifikasi.');
        }

        $pembayaran->load([
            'sewaRuko.penyewa.user',
            'sewaRuko.ruko.kategori',
            'sewaRuko.dokumen',
            'tipe',
        ]);

        $namaFile = 'kwitansi-' . str_replace('/', '-', $pembayaran->no_kwitansi) . '.pdf';

        $pdf = Pdf::loadView('adminkantin.pembayaran.kwitansi', compact('pembayaran'))
            ->setPaper('a5', 'portrait');

        // Simpan ke storage
        $path = 'kwitansi/' . $namaFile;
        Storage::disk('public')->put($path, $pdf->output());

        // Simpan path ke database jika belum ada
        if (!$pembayaran->path_kwitansi) {
            $pembayaran->update(['path_kwitansi' => $path]);
        }

        return $pdf->download($namaFile);
    }
}
