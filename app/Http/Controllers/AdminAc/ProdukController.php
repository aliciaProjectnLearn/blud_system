<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\KategoriKomponen;
use App\Models\Produk;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    use Loggable;

    public function index(Request $request)
    {
        $search      = $request->input('search');
        $kategori_id = $request->input('id_kategori_komponen');

        $query = Produk::with('kategori');

        if ($search) {
            $query->where('nama_produk', 'like', '%' . $search . '%');
        }

        if ($kategori_id) {
            $query->where('id_kategori_komponen', $kategori_id);
        }

        $produks   = $query->latest()->paginate(10);
        $kategoris = KategoriKomponen::all();

        return view('adminac.produk.index', compact('produks', 'kategoris', 'search', 'kategori_id'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        KategoriKomponen::create([
            'nama' => $request->nama,
        ]);

        $this->function_log('AC', 'create', 'Admin AC menambahkan kategori komponen baru: ' . $request->nama);

        return redirect()->back()
            ->with('success', 'Kategori Komponen baru berhasil ditambahkan.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk'          => 'required|string|max:255',
            'id_kategori_komponen' => 'required|exists:kategori_komponens,id',
            'satuan'               => 'required|string|max:50',
            'harga'                => 'required|numeric|min:0',
            'stok'                 => 'required|integer|min:0',
            'deskripsi'            => 'nullable|string',
        ]);

        Produk::create($validated);

        $this->function_log('AC', 'create', 'Admin AC menambahkan produk baru: ' . $request->nama_produk);

        return redirect()->route('admin.ac.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama_produk'          => 'required|string|max:255',
            'id_kategori_komponen' => 'required|exists:kategori_komponens,id',
            'satuan'               => 'required|string|max:50',
            'harga'                => 'required|numeric|min:0',
            'stok'                 => 'required|integer|min:0',
            'deskripsi'            => 'nullable|string',
        ]);

        $produk->update($validated);

        $this->function_log('AC', 'update', 'Admin AC mengubah produk: ' . $produk->nama_produk);

        return redirect()->route('admin.ac.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $namaProduk = $produk->nama_produk;
        $produk->delete();

        $this->function_log('AC', 'delete', 'Admin AC menghapus produk: ' . $namaProduk);

        return redirect()->route('admin.ac.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function updateStok(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'stok' => 'required|integer|min:0',
        ]);

        $stokLama = $produk->stok;
        $produk->update(['stok' => $validated['stok']]);

        $this->function_log('AC', 'update', 'Admin AC mengubah stok produk: ' . $produk->nama_produk . ' (' . $stokLama . ' → ' . $validated['stok'] . ')');

        return redirect()->route('admin.ac.produk.index')
            ->with('success', 'Stok produk berhasil diperbarui.');
    }
}