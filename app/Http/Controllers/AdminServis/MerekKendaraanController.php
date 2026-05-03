<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\MerekKendaraan;
use Illuminate\Http\Request;

class MerekKendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = MerekKendaraan::withCount('modelKendaraan');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $merek = $query->latest()->paginate(10)->withQueryString();

        return view('adminservis.kendaraan.merek', compact('merek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:motor,mobil',
            'is_active' => 'boolean',
        ]);

        MerekKendaraan::create([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.servis.merek.index')
            ->with('success', 'Merek kendaraan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $merek = MerekKendaraan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:motor,mobil',
            'is_active' => 'boolean',
        ]);

        $merek->update([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.servis.merek.index')
            ->with('success', 'Merek kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $merek = MerekKendaraan::withCount('modelKendaraan')->findOrFail($id);

        if ($merek->model_kendaraan_count > 0) {
            return redirect()->back()
                ->with('error', 'Merek tidak dapat dihapus karena masih ada model yang terhubung.');
        }

        $merek->delete();

        return redirect()->route('admin.servis.merek.index')
            ->with('success', 'Merek kendaraan berhasil dihapus.');
    }
}
