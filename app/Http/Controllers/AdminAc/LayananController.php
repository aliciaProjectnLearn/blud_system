<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\LayananAc;
use App\Models\Kategori;
use App\Models\BookingAc;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    use Loggable;

    public function index(Request $request)
    {
        $query = LayananAc::with('kategori');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }

        $layanans  = $query->paginate(10)->withQueryString();
        $kategoris = Kategori::where('tipe', 'ac')->get();

        return view('adminac.layanan.index', compact('layanans', 'kategoris'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama',
        ]);

        Kategori::create([
            'nama' => $request->nama,
            'tipe' => 'ac',
        ]);

        $this->function_log('AC', 'create', 'Admin AC menambahkan kategori layanan baru: ' . $request->nama);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function create()
    {
        $kategoris = Kategori::where('tipe', 'ac')->get();
        return view('adminac.layanan.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama'        => 'required|string|max:255',
            'kapasitas_ac' => 'nullable|string|max:50',
            'harga_jasa'  => 'required|numeric|min:0',
        ]);

        LayananAc::create($request->all());

        $this->function_log('AC', 'create', 'Admin AC menambahkan layanan baru: ' . $request->nama);

        return redirect()->route('admin.ac.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $layanan   = LayananAc::findOrFail($id);
        $kategoris = Kategori::where('tipe', 'ac')->get();
        return view('adminac.layanan.edit', compact('layanan', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_id'  => 'required|exists:kategori,id',
            'nama'         => 'required|string|max:255',
            'kapasitas_ac' => 'nullable|string|max:50',
            'harga_jasa'   => 'required|numeric|min:0',
        ]);

        $layanan = LayananAc::findOrFail($id);
        $layanan->update($request->all());

        $this->function_log('AC', 'update', 'Admin AC mengubah layanan: ' . $layanan->nama);

        return redirect()->route('admin.ac.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $layanan = LayananAc::findOrFail($id);

        $activeBookings = BookingAc::where('layanan_id', $id)
            ->whereIn('status', ['menunggu', 'proses', 'pending'])
            ->exists();

        if ($activeBookings) {
            return redirect()->back()
                ->with('error', 'Layanan tidak dapat dihapus karena masih memiliki booking yang aktif.');
        }

        $namaLayanan = $layanan->nama;
        $layanan->delete();

        $this->function_log('AC', 'delete', 'Admin AC menghapus layanan: ' . $namaLayanan);

        return redirect()->back()
            ->with('success', 'Layanan berhasil dihapus.');
    }
}