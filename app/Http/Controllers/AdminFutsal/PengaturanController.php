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
            [
                'jam_buka'           => '07:00:00',
                'jam_tutup'          => '22:00:00',
                'jam_blokir_aktif'   => false,
                'jam_blokir_mulai'   => '07:00:00',
                'jam_blokir_selesai' => '15:00:00',
                'hari_blokir'        => 'Senin,Selasa,Rabu,Kamis,Jumat',
                'keterangan_blokir'  => 'Jam kegiatan sekolah',
            ]
        );
        return view('adminfutsal.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jam_buka'            => 'required',
            'jam_tutup'           => 'required',
            'harga_reguler_futsal' => 'required|numeric|min:0',
            'harga_event_futsal'  => 'required|numeric|min:0',
            'jam_blokir_aktif'   => 'boolean',
            'jam_blokir_mulai'   => 'required_if:jam_blokir_aktif,1|nullable',
            'jam_blokir_selesai' => 'required_if:jam_blokir_aktif,1|nullable',
            'hari_blokir'        => 'nullable|array',
            'keterangan_blokir'  => 'nullable|string|max:100',
        ]);

        $pengaturan = Pengaturan::findOrFail($id);
        $pengaturan->update([
            'jam_buka'            => $request->jam_buka,
            'jam_tutup'           => $request->jam_tutup,
            'harga_reguler_futsal' => $request->harga_reguler_futsal,
            'harga_event_futsal'  => $request->harga_event_futsal,
            'jam_blokir_aktif'   => $request->boolean('jam_blokir_aktif'),
            'jam_blokir_mulai'   => $request->jam_blokir_mulai,
            'jam_blokir_selesai' => $request->jam_blokir_selesai,
            'hari_blokir'        => $request->hari_blokir
                                    ? implode(',', $request->hari_blokir)
                                    : null,
            'keterangan_blokir'  => $request->keterangan_blokir,
        ]);

        return redirect()->route('admin.futsal.pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    // Route jam-operasional sudah dihapus, method ini tidak dipakai lagi
    // Dibiarkan untuk referensi/rollback jika diperlukan
    //
    // public function indexJamOperasional()
    // {
    //     $lapangans = \App\Models\Lapangan::with('jamOperasional')->get();
    //     return view('adminfutsal.pengaturan.jam_operasional', compact('lapangans'));
    // }
    //
    // public function storeJamOperasional(Request $request)
    // {
    //     $request->validate([
    //         'lapangan_id' => 'required|exists:lapangan,id',
    //         'jam' => 'required|array',
    //     ]);
    //
    //     foreach ($request->jam as $hari => $data) {
    //         \App\Models\JamOperasionalLapangan::updateOrCreate(
    //             [
    //                 'lapangan_id' => $request->lapangan_id,
    //                 'hari' => $hari,
    //             ],
    //             [
    //                 'jam_buka' => $data['jam_buka'] ?? '07:00:00',
    //                 'jam_tutup' => $data['jam_tutup'] ?? '22:00:00',
    //                 'is_aktif' => isset($data['is_aktif']) ? true : false,
    //             ]
    //         );
    //     }
    //
    //     return back()->with('success', 'Jam operasional berhasil disimpan.');
    // }
}
