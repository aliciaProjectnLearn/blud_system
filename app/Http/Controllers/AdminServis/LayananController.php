<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\LayananServis;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    use Loggable;

    public function index(Request $request)
    {
        $query = LayananServis::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_layanan', 'like', '%' . $request->search . '%')
                  ->orWhere('tipe_kendaraan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tipe_kendaraan')) {
            $query->where('tipe_kendaraan', $request->tipe_kendaraan);
        }

        $layanan = $query->latest()->paginate(10)->withQueryString();

        return view('adminservis.layanan.index', compact('layanan'));
    }

    public function create()
    {
        return view('adminservis.layanan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan'   => 'required|string|max:255',
            'tipe_kendaraan' => 'required|in:motor,mobil,keduanya',
            'harga_estimasi' => 'required|numeric|min:0',
            'deskripsi'      => 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        LayananServis::create([
            'nama_layanan'   => $request->nama_layanan,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'harga_estimasi' => $request->harga_estimasi,
            'deskripsi'      => $request->deskripsi,
            'is_active'      => $request->has('is_active') ? true : false,
        ]);

        $this->function_log('Servis', 'create', 'Admin Servis menambahkan layanan baru: ' . $request->nama_layanan);

        return redirect()->route('admin.servis.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(LayananServis $layanan)
    {
        return view('adminservis.layanan.edit', compact('layanan'));
    }

    public function update(Request $request, LayananServis $layanan)
    {
        $request->validate([
            'nama_layanan'   => 'required|string|max:255',
            'tipe_kendaraan' => 'required|in:motor,mobil,keduanya',
            'harga_estimasi' => 'required|numeric|min:0',
            'deskripsi'      => 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        $layanan->update([
            'nama_layanan'   => $request->nama_layanan,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'harga_estimasi' => $request->harga_estimasi,
            'deskripsi'      => $request->deskripsi,
            'is_active'      => $request->has('is_active') ? true : false,
        ]);

        $this->function_log('Servis', 'update', 'Admin Servis mengubah layanan: ' . $layanan->nama_layanan);

        return redirect()->route('admin.servis.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(LayananServis $layanan)
    {
        if ($layanan->bookingServis()->exists()) {
            return redirect()->back()
                ->with('error', 'Layanan tidak dapat dihapus karena sudah 
                    digunakan dalam transaksi servis.');
        }

        $namaLayanan = $layanan->nama_layanan;
        $layanan->delete();

        $this->function_log('Servis', 'delete', 'Admin Servis menghapus layanan: ' . $namaLayanan);

        return redirect()->route('admin.servis.layanan.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }
}