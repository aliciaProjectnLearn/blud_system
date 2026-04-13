<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\Ruko;
use App\Models\DokumentasiUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class UnitController extends Controller
{
    // ────────────────────────────────────────────────────────────────
    //  HELPER: Auto-generate kode_unit  →  UNT001, UNT002, dst.
    // ────────────────────────────────────────────────────────────────
    private function generateKodeUnit(): string
    {
        $last = Ruko::whereNotNull('kode_unit')
            ->orderByRaw("CAST(SUBSTRING(kode_unit, 4) AS UNSIGNED) DESC")
            ->value('kode_unit');

        if ($last) {
            $lastNumber = (int) substr($last, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'UNT' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // ────────────────────────────────────────────────────────────────
    //  HELPER: Simpan multiple file dokumentasi
    // ────────────────────────────────────────────────────────────────
    /**
     * @param int $rukoId
     * @param array $files
     * @return array Mengembalikan array path file yang berhasil diupload (untuk keperluan rollback manual jika terjadi error DB)
     * @throws Exception
     */
    private function simpanDokumentasi(int $rukoId, array $files): array
    {
        $uploadedPaths = [];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        foreach ($files as $file) {
            if ($file->isValid()) {
                // [OPTIMALISASI] Error checking pada proses upload
                $path = $file->store('dokumentasi_unit', 'public');
                
                if (!$path) {
                    throw new Exception("Gagal mengunggah file {$file->getClientOriginalName()}. Pastikan storage dapat ditulis.");
                }
                
                $uploadedPaths[] = $path;
                
                $ext = strtolower($file->getClientOriginalExtension());
                // [OPTIMALISASI] Tipe dokumen disederhanakan: gambar vs dokumen biasa
                $tipe = in_array($ext, $imageExtensions) ? 'gambar' : 'dokumen';
                $namaAsli = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                DokumentasiUnit::create([
                    'ruko_id'       => $rukoId,
                    'file'          => $path,
                    'tipe'          => $tipe,
                    'judul_dokumen' => $namaAsli,
                ]);
            }
        }

        return $uploadedPaths;
    }

    // ────────────────────────────────────────────────────────────────
    //  INDEX  (dengan filter kategori & status)
    // ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Ruko::with(['kategori', 'dokumentasiUnit']);

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status_unit')) {
            $query->where('status_unit', $request->status_unit);
        }

        $units      = $query->orderByRaw("CAST(SUBSTRING(kode_unit, 4) AS UNSIGNED) ASC")->paginate(10)->withQueryString();
        $kategoris  = \App\Models\Kategori::where('tipe', 'kantin')->orderBy('nama')->get();

        return view('adminkantin.unit.index', compact('units', 'kategoris'));
    }

    // ────────────────────────────────────────────────────────────────
    //  CREATE
    // ────────────────────────────────────────────────────────────────
    public function create()
    {
        $kodeUnit  = $this->generateKodeUnit();
        $kategoris = \App\Models\Kategori::where('tipe', 'kantin')->orderBy('nama')->get();

        return view('adminkantin.unit.create', compact('kodeUnit', 'kategoris'));
    }

    // ────────────────────────────────────────────────────────────────
    //  STORE
    // ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_unit'    => 'required|string|max:10|unique:ruko,kode_unit',
            'kategori_id'  => 'required|exists:kategori,id',
            'harga'        => 'required|numeric|min:0',
            'dokumen'      => 'nullable|array|max:10',
            'dokumen.*'    => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $uploadedPaths = [];

        // [OPTIMALISASI] Gunakan DB Transaction agar parent (ruko) dan child (dokumentasi) atomic
        DB::beginTransaction();
        try {
            $ruko = Ruko::create([
                'kode_unit'   => $validated['kode_unit'],
                'kategori_id' => $validated['kategori_id'],
                'harga'       => $validated['harga'],
                'status_unit' => 'kosong',
            ]);

            if ($request->hasFile('dokumen')) {
                // Eksekusi Helper yang sudah di-refactor
                $uploadedPaths = $this->simpanDokumentasi($ruko->id, $request->file('dokumen'));
            }

            DB::commit();

            return redirect()
                ->route('adminkantin.unit.index')
                ->with('success', "Unit {$ruko->kode_unit} berhasil ditambahkan.");

        } catch (Exception $e) {
            DB::rollBack();
            
            // [OPTIMALISASI] Hapus file fisik (Orphan Files) di storage jika proses gagal di tengah jalan
            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ────────────────────────────────────────────────────────────────
    //  SHOW
    // ────────────────────────────────────────────────────────────────
    public function show(Ruko $unit)
    {
        $unit->load(['kategori', 'sewaRuko', 'dokumentasiUnit']);
        return view('adminkantin.unit.show', compact('unit'));
    }

    // ────────────────────────────────────────────────────────────────
    //  EDIT
    // ────────────────────────────────────────────────────────────────
    public function edit(Ruko $unit)
    {
        $unit->load('dokumentasiUnit');
        $kategoris = \App\Models\Kategori::where('tipe', 'kantin')->orderBy('nama')->get();

        return view('adminkantin.unit.edit', compact('unit', 'kategoris'));
    }

    // ────────────────────────────────────────────────────────────────
    //  UPDATE
    // ────────────────────────────────────────────────────────────────
    public function update(Request $request, Ruko $unit)
    {
        $validated = $request->validate([
            'kategori_id'       => 'required|exists:kategori,id',
            'harga'             => 'required|numeric|min:0',
            'status_unit'       => 'required|in:terisi,kosong',
            'dokumen'           => 'nullable|array|max:10',
            'dokumen.*'         => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'hapus_dokumen'     => 'nullable|array',
            'hapus_dokumen.*'   => 'integer|exists:dokumentasi_unit,id',
        ]);

        $uploadedPaths = [];

        DB::beginTransaction();
        try {
            $unit->update([
                'kategori_id' => $validated['kategori_id'],
                'harga'       => $validated['harga'],
                'status_unit' => $validated['status_unit'],
            ]);

            // Hapus dokumen lama yang dipilih
            if (!empty($validated['hapus_dokumen'])) {
                $dokumenLama = DokumentasiUnit::whereIn('id', $validated['hapus_dokumen'])
                    ->where('ruko_id', $unit->id)
                    ->get();

                foreach ($dokumenLama as $dok) {
                    // Cek ketersediaan file sebelum hapus untuk mencegah error Not Found
                    if (Storage::disk('public')->exists($dok->file)) {
                        Storage::disk('public')->delete($dok->file);
                    }
                    $dok->delete();
                }
            }

            // Simpan dokumen baru
            if ($request->hasFile('dokumen')) {
                $uploadedPaths = $this->simpanDokumentasi($unit->id, $request->file('dokumen'));
            }

            DB::commit();

            return redirect()
                ->route('adminkantin.unit.index')
                ->with('success', "Unit {$unit->kode_unit} berhasil diperbarui.");

        } catch (Exception $e) {
            DB::rollBack();

            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()
                ->with('error', 'Terjadi kesalahan sistem saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ────────────────────────────────────────────────────────────────
    //  DESTROY
    // ────────────────────────────────────────────────────────────────
    public function destroy(Ruko $unit)
    {
        // [OPTIMALISASI] Validasi status_unit dipindah paling atas sebelum DB call berat
        if ($unit->status_unit === 'terisi') {
            return back()->with(
                'error',
                "Unit {$unit->kode_unit} tidak dapat dihapus karena sedang berstatus 'Terisi'. Pastikan unit sudah dikosongkan."
            );
        }

        DB::beginTransaction();
        try {
            // Hapus file fisik dengan mengecek eksistensinya lebih dulu
            foreach ($unit->dokumentasiUnit as $dok) {
                if (Storage::disk('public')->exists($dok->file)) {
                    Storage::disk('public')->delete($dok->file);
                }
            }

            $kode = $unit->kode_unit;
            // Record dokumentasi akan terhapus via CASCADE di database secara efisien
            $unit->delete();

            DB::commit();

            return redirect()
                ->route('adminkantin.unit.index')
                ->with('success', "Unit {$kode} berhasil dihapus.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────────────
    //  UPDATE DETAIL DOKUMEN  (AJAX - PATCH)
    // ────────────────────────────────────────────────────────────────
    public function updateDokumenDetail(Request $request, DokumentasiUnit $dokumen)
    {
        // [OPTIMALISASI] AJAX Error handling (try-catch) pada controller API
        try {
            $validated = $request->validate([
                'judul_dokumen' => 'required|string|max:150',
                'deskripsi'     => 'nullable|string',
            ]);

            $dokumen->update([
                'judul_dokumen' => $validated['judul_dokumen'],
                'deskripsi'     => $validated['deskripsi'],
            ]);

            return response()->json([
                'success'       => true,
                'judul_dokumen' => $dokumen->fresh()->judul_dokumen,
                'deskripsi'     => $dokumen->fresh()->deskripsi ?? '-',
                'message'       => 'Detail dokumen berhasil diperbarui.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage()
            ], 500);
        }
    }
}
