<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProdukServis;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = ProdukServis::query();

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tipe_kendaraan')) {
            $query->where('tipe_kendaraan', $request->tipe_kendaraan);
        }

        $produks = $query->latest()->paginate(10);

        return view('adminservis.produk.index', compact('produks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'tipe_kendaraan' => 'required|in:motor,mobil',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'kode_part' => 'nullable|string|max:100',
            'merk' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
        ]);

        if (empty($validated['satuan'])) {
            $validated['satuan'] = 'pcs';
        }

        ProdukServis::create($validated);

        return redirect()->route('admin.servis.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, ProdukServis $produk)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'tipe_kendaraan' => 'required|in:motor,mobil',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'kode_part' => 'nullable|string|max:100',
            'merk' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
        ]);

        if (empty($validated['satuan'])) {
            $validated['satuan'] = 'pcs';
        }

        $produk->update($validated);

        return redirect()->route('admin.servis.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(ProdukServis $produk)
    {
        if ($produk->rincianServis()->exists()) {
            return redirect()->route('admin.servis.produk.index')->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam rincian servis.');
        }

        $produk->delete();

        return redirect()->route('admin.servis.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
