<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    use Loggable;

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

        $this->function_log('Futsal', 'create', 'Admin Futsal menambahkan lapangan baru: ' . $request->nama);

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

        $this->function_log('Futsal', 'update', 'Admin Futsal mengubah lapangan: ' . $lapangan->nama);

        return redirect()->route('admin.futsal.lapangan.index')
            ->with('success', 'Lapangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $lapangan = Lapangan::findOrFail($id);

        $hasBooking = \App\Models\BookingFutsal::where('lapangan_id', $id)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->exists();

        if ($hasBooking) {
            return back()->with('error', 'Lapangan tidak dapat dihapus karena masih ada booking aktif.');
        }

        if ($lapangan->foto) {
            Storage::disk('public')->delete($lapangan->foto);
        }

        $namaLapangan = $lapangan->nama;
        $lapangan->delete();

        $this->function_log('Futsal', 'delete', 'Admin Futsal menghapus lapangan: ' . $namaLapangan);

        return redirect()->route('admin.futsal.lapangan.index')
            ->with('success', 'Lapangan berhasil dihapus.');
    }
}