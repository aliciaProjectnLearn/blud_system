<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    public function index()
    {
        $lapangans = Lapangan::withCount('jamOperasional')->orderBy('id')->get();
        return view('adminfutsal.lapangan.index', compact('lapangans'));
    }

    public function create()
    {
        return view('adminfutsal.lapangan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'ukuran'      => 'nullable|string|max:50',
            'deskripsi'   => 'nullable|string',
            'spesifikasi' => 'nullable|string',
            'lokasi'      => 'nullable|string|max:255',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('lapangan', 'public');
        }

        Lapangan::create([
            'nama'        => $request->nama,
            'ukuran'      => $request->ukuran,
            'deskripsi'   => $request->deskripsi,
            'spesifikasi' => $request->spesifikasi,
            'lokasi'      => $request->lokasi,
            'foto'        => $fotoPath,
        ]);

        return redirect()->route('admin.futsal.lapangan.index')
            ->with('success', 'Lapangan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        return view('adminfutsal.lapangan.edit', compact('lapangan'));
    }

    public function update(Request $request, $id)
    {
        $lapangan = Lapangan::findOrFail($id);

        $request->validate([
            'nama'        => 'required|string|max:100',
            'ukuran'      => 'nullable|string|max:50',
            'deskripsi'   => 'nullable|string',
            'spesifikasi' => 'nullable|string',
            'lokasi'      => 'nullable|string|max:255',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = $lapangan->foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($lapangan->foto) {
                Storage::disk('public')->delete($lapangan->foto);
            }
            $fotoPath = $request->file('foto')->store('lapangan', 'public');
        }

        $lapangan->update([
            'nama'        => $request->nama,
            'ukuran'      => $request->ukuran,
            'deskripsi'   => $request->deskripsi,
            'spesifikasi' => $request->spesifikasi,
            'lokasi'      => $request->lokasi,
            'foto'        => $fotoPath,
        ]);

        return redirect()->route('admin.futsal.lapangan.index')
            ->with('success', 'Lapangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $lapangan = Lapangan::findOrFail($id);

        // Cek apakah masih ada booking aktif
        $hasBooking = \App\Models\BookingFutsal::where('lapangan_id', $id)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->exists();

        if ($hasBooking) {
            return back()->with('error', 'Lapangan tidak dapat dihapus karena masih ada booking aktif.');
        }

        if ($lapangan->foto) {
            Storage::disk('public')->delete($lapangan->foto);
        }

        $lapangan->delete();

        return redirect()->route('admin.futsal.lapangan.index')
            ->with('success', 'Lapangan berhasil dihapus.');
    }
}
