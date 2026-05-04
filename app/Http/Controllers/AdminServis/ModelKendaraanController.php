<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\ModelKendaraan;
use App\Models\MerekKendaraan;
use Illuminate\Http\Request;

class ModelKendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = ModelKendaraan::with('merek');

        if ($request->filled('search')) {
            $query->where('nama_model', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('merek_id')) {
            $query->where('merek_kendaraan_id', $request->merek_id);
        }

        $model = $query->latest()->paginate(10)->withQueryString();
        $mereks = MerekKendaraan::orderBy('nama')->get();

        return view('adminservis.kendaraan.model', compact('model', 'mereks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'merek_kendaraan_id' => 'required|exists:merek_kendaraan,id',
            'nama_model'         => 'required|string|max:255',
            'is_active'          => 'boolean',
        ]);

        ModelKendaraan::create([
            'merek_kendaraan_id' => $request->merek_kendaraan_id,
            'nama_model'         => $request->nama_model,
            'is_active'          => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.servis.model.index')
            ->with('success', 'Model kendaraan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $model = ModelKendaraan::findOrFail($id);

        $request->validate([
            'merek_kendaraan_id' => 'required|exists:merek_kendaraan,id',
            'nama_model'         => 'required|string|max:255',
            'is_active'          => 'boolean',
        ]);

        $model->update([
            'merek_kendaraan_id' => $request->merek_kendaraan_id,
            'nama_model'         => $request->nama_model,
            'is_active'          => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.servis.model.index')
            ->with('success', 'Model kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $model = ModelKendaraan::findOrFail($id);
        $model->delete();

        return redirect()->route('admin.servis.model.index')
            ->with('success', 'Model kendaraan berhasil dihapus.');
    }
}
