<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SewaRuko;
use App\Models\Ruko;
use App\Models\Penyewa;
use App\Models\DokumenPenyewaan;
use App\Http\Requests\AdminKantin\UpdatePenyewaanRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PenyewaanController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // 1. Ambil semua data untuk auto-status logic
        $allData = SewaRuko::with('ruko')->get();

        foreach ($allData as $item) {
            // HITUNG STATUS PENYEWAAN
            $newStatus = $today->gt(Carbon::parse($item->tgl_selesai)) ? 'selesai' : 'aktif';

            if ($item->status !== $newStatus) {
                $item->update(['status' => $newStatus]);
            }

            // UPDATE STATUS RUKO
            if ($item->status === 'aktif') {
                $item->ruko->update(['status_unit' => 'terisi']);
            } else {
                // Cek apakah ada penyewaan aktif lain untuk ruko ini
                $masihDisewa = SewaRuko::where('ruko_id', $item->ruko_id)
                    ->where('status', 'aktif')
                    ->where('id', '!=', $item->id)
                    ->exists();

                if (!$masihDisewa) {
                    $item->ruko->update(['status_unit' => 'kosong']);
                }
            }
        }

        // 2. Query data dengan filter
        $query = SewaRuko::with(['penyewa.user', 'ruko']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->ruko_id) {
            $query->where('ruko_id', $request->ruko_id);
        }

        if ($request->penyewa_id) {
            $query->where('penyewa_id', $request->penyewa_id);
        }

        $data = $query->latest()->get();
        $rukos = Ruko::all();
        $penyewas = Penyewa::all();

        return view('adminkantin.penyewaan.index', compact('data', 'rukos', 'penyewas'));
    }

    public function show($id)
    {
        $data = SewaRuko::with(['penyewa.user', 'ruko', 'dokumen'])
            ->findOrFail($id);

        return view('adminkantin.penyewaan.show', compact('data'));
    }

    public function update(UpdatePenyewaanRequest $request, $id)
    {
        $sewa = SewaRuko::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $sewa->update($request->validated());

            // Trigger re-check status logic
            $this->recalculateStatuses();

            DB::commit();
            return redirect()->back()->with('success', 'Data penyewaan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $sewa = SewaRuko::findOrFail($id);

        if ($sewa->status === 'aktif') {
            return redirect()->back()->with('error', 'Penyewaan yang berstatus aktif tidak dapat dihapus.');
        }

        $sewa->delete();

        return redirect()->route('adminkantin.penyewaan.index')->with('success', 'Data penyewaan berhasil dihapus.');
    }

    /**
     * Recalculate statuses for all rentals and units.
     */
    private function recalculateStatuses()
    {
        $today = Carbon::today();
        $allData = SewaRuko::with('ruko')->get();

        foreach ($allData as $item) {
            $newStatus = $today->gt(Carbon::parse($item->tgl_selesai)) ? 'selesai' : 'aktif';
            if ($item->status !== $newStatus) {
                $item->update(['status' => $newStatus]);
            }

            if ($item->status === 'aktif') {
                $item->ruko->update(['status_unit' => 'terisi']);
            } else {
                $masihDisewa = SewaRuko::where('ruko_id', $item->ruko_id)
                    ->where('status', 'aktif')
                    ->where('id', '!=', $item->id)
                    ->exists();

                if (!$masihDisewa) {
                    $item->ruko->update(['status_unit' => 'kosong']);
                }
            }
        }
    }

    // --- DOCUMENT METHODS ---

    public function uploadDokumen(Request $request, $id)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        $sewa = SewaRuko::findOrFail($id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('dokumen_penyewaan', $filename, 'public');

            // Generate No MOU otomatis: MOU/TAHUN/BULAN/HARI/RANDOM
            $no_mou = 'MOU/' . date('Y/m/d') . '/' . strtoupper(bin2hex(random_bytes(3)));

            DokumenPenyewaan::create([
                'sewa_id' => $sewa->id,
                'no_mou' => $no_mou,
                'nama_dokumen' => $request->nama_dokumen,
                'path_file' => $path,
            ]);

            return redirect()->back()->with('success', 'Dokumen berhasil diunggah dengan No. MOU: ' . $no_mou);
        }

        return redirect()->back()->with('error', 'Gagal mengunggah dokumen.');
    }

    public function downloadDokumen($id)
    {
        $doc = DokumenPenyewaan::findOrFail($id);
        
        if (!Storage::disk('public')->exists($doc->path_file)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        return Storage::disk('public')->download($doc->path_file, $doc->nama_dokumen);
    }

    public function hapusDokumen($id)
    {
        $doc = DokumenPenyewaan::findOrFail($id);

        if (Storage::disk('public')->exists($doc->path_file)) {
            Storage::disk('public')->delete($doc->path_file);
        }

        $doc->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function generateMOU($id)
    {
        return DB::transaction(function () use ($id) {
            $sewa = SewaRuko::with(['penyewa.user', 'ruko.kategori'])->findOrFail($id);

            // 1. Generate No MOU (Format: MOU/Kantin/[ID]/[YEAR])
            $no_mou = "MOU/Kantin/{$sewa->id}/" . date('Y');

            // 2. Load View PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('adminkantin.penyewaan.pdf_mou', [
                'sewa'   => $sewa,
                'no_mou' => $no_mou
            ]);

            // 3. Nama File & Path (Simpan ke folder 'mou')
            $nama_file = 'MOU_' . str_replace('/', '_', $no_mou) . '_' . time() . '.pdf';
            $path = 'mou/' . $nama_file;

            // 4. Constraint: Hindari duplikasi / Update record & hapus file lama
            $existingDoc = DokumenPenyewaan::where('sewa_id', $sewa->id)
                ->where('nama_dokumen', 'LIKE', 'MOU Otomatis%')
                ->first();

            if ($existingDoc) {
                // Hapus file lama jika ada
                if (Storage::disk('public')->exists($existingDoc->path_file)) {
                    Storage::disk('public')->delete($existingDoc->path_file);
                }
                
                // Update record yang ada
                $existingDoc->update([
                    'no_mou'       => $no_mou,
                    'nama_dokumen' => 'Dokumen MOU Perjanjian Sewa - ' . $sewa->penyewa->nama_usaha,
                    'path_file'    => $path,
                ]);
            } else {
                // Buat record baru
                DokumenPenyewaan::create([
                    'sewa_id'      => $sewa->id,
                    'no_mou'       => $no_mou,
                    'nama_dokumen' => 'Dokumen MOU Perjanjian Sewa - ' . $sewa->penyewa->nama_usaha,
                    'path_file'    => $path,
                ]);
            }

            // 5. Simpan (Upload) ke Storage
            Storage::disk('public')->put($path, $pdf->output());

            // 6. Return response download otomatis
            return $pdf->download($nama_file);
        });
    }
}
