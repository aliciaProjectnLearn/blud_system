<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pengaturan;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::firstOrCreate(
            ['id' => 1],
            ['jam_buka' => '07:00:00', 'jam_tutup' => '22:00:00']
        );
        return view('adminfutsal.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jam_buka' => 'required',
            'jam_tutup' => 'required',
            'harga_reguler_futsal' => 'required|numeric|min:0',
            'harga_event_futsal' => 'required|numeric|min:0'
        ]);

        $pengaturan = Pengaturan::findOrFail($id);
        $pengaturan->update([
            'jam_buka' => $request->jam_buka,
            'jam_tutup' => $request->jam_tutup,
            'harga_reguler_futsal' => $request->harga_reguler_futsal,
            'harga_event_futsal' => $request->harga_event_futsal
        ]);

        return redirect()->route('adminfutsal.pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
