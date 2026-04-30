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

        return redirect()->route('admin.futsal.pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function indexJamOperasional()
    {
        $lapangans = \App\Models\Lapangan::with('jamOperasional')->get();
        return view('adminfutsal.pengaturan.jam_operasional', compact('lapangans'));
    }

    public function storeJamOperasional(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangan,id',
            'jam' => 'required|array',
        ]);

        foreach ($request->jam as $hari => $data) {
            \App\Models\JamOperasionalLapangan::updateOrCreate(
                [
                    'lapangan_id' => $request->lapangan_id,
                    'hari' => $hari,
                ],
                [
                    'jam_buka' => $data['jam_buka'] ?? '07:00:00',
                    'jam_tutup' => $data['jam_tutup'] ?? '22:00:00',
                    'is_aktif' => isset($data['is_aktif']) ? true : false,
                ]
            );
        }

        return back()->with('success', 'Jam operasional berhasil disimpan.');
    }
}
