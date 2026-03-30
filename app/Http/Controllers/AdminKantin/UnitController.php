<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\Ruko;
use App\Models\DokumentasiUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    // ────────────────────────────────────────────────────────────────
    //  HELPER: Auto-generate kode_unit  →  UNT001, UNT002, dst.
    // ────────────────────────────────────────────────────────────────
    private function generateKodeUnit(): string
    {
        // Ambil kode_unit terbesar yang sudah ada
        $last = Ruko::whereNotNull('kode_unit')
            ->orderByRaw("CAST(SUBSTRING(kode_unit, 4) AS UNSIGNED) DESC")
            ->value('kode_unit');

        if ($last) {
            // Ambil angka di belakang prefix "UNT" lalu tambah 1
            $lastNumber = (int) substr($last, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Format: UNT + angka 3 digit dengan leading-zero
        return 'UNT' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // ────────────────────────────────────────────────────────────────
    //  HELPER: Simpan multiple file dokumentasi
    // ────────────────────────────────────────────────────────────────
    private function simpanDokumentasi(int $rukoId, array $files): void
    {
        // ── Logic Batch Upload ────────────────────────────────────
        // 1. Cek apakah SEMUA file yang diupload adalah gambar
        $allImages = true;
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        foreach ($files as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $imageExtensions)) {
                $allImages = false;
                break;
            }
        }

        // 2. Tentukan apakah paksa 'umum' (jika semua gambar DAN jumlah > 1)
        $forceUmum = ($allImages && count($files) > 1);

        foreach ($files as $file) {
            if ($file->isValid()) {
                $path = $file->store('dokumentasi_unit', 'public');
                $ext  = strtolower($file->getClientOriginalExtension());

                // Jika forceUmum = true, isi 'umum'. Jika tidak, isi ekstensi aslinya.
                $tipe = $forceUmum ? 'umum' : ($ext ?: 'umum');
                
                // Ambil nama asli file tanpa ekstensi untuk judul_dokumen
                $namaAsli = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                DokumentasiUnit::create([
                    'ruko_id'       => $rukoId,
                    'file'          => $path,
                    'tipe'          => $tipe,
                    'judul_dokumen' => $namaAsli,
                ]);
            }
        }
    }

    // ────────────────────────────────────────────────────────────────
    //  INDEX  (dengan filter kategori & status)
    // ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Ruko::with(['kategori', 'dokumentasiUnit']);

        // Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter Status
        if ($request->filled('status_unit')) {
            $query->where('status_unit', $request->status_unit);
        }

        $units      = $query->orderByRaw("CAST(SUBSTRING(kode_unit, 4) AS UNSIGNED) ASC")->paginate(10)->withQueryString();
        $kategoris  = \App\Models\Kategori::orderBy('nama')->get();

        return view('adminkantin.unit.index', compact('units', 'kategoris'));
    }

    // ────────────────────────────────────────────────────────────────
    //  CREATE  (auto-generate kode_unit, lempar ke view sebagai read-only)
    // ────────────────────────────────────────────────────────────────
    public function create()
    {
        $kodeUnit  = $this->generateKodeUnit();
        $kategoris = \App\Models\Kategori::orderBy('nama')->get();

        return view('adminkantin.unit.create', compact('kodeUnit', 'kategoris'));
    }

    // ────────────────────────────────────────────────────────────────
    //  STORE  (multiple file upload)
    // ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_unit'    => 'required|string|max:10|unique:ruko,kode_unit',
            'kategori_id'  => 'required|exists:kategori,id',
            'status_unit'  => 'required|in:terisi,kosong',
            // Array file: opsional, maks 10 file, masing-masing maks 5 MB
            'dokumen'      => 'nullable|array|max:10',
            'dokumen.*'    => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $ruko = Ruko::create([
            'kode_unit'   => $validated['kode_unit'],
            'kategori_id' => $validated['kategori_id'],
            'status_unit' => $validated['status_unit'],
        ]);

        // Simpan multiple file dokumentasi (jika ada)
        if ($request->hasFile('dokumen')) {
            $this->simpanDokumentasi(
                $ruko->id,
                $request->file('dokumen')
            );
        }

        return redirect()
            ->route('adminkantin.unit.index')
            ->with('success', "Unit {$ruko->kode_unit} berhasil ditambahkan.");
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
        $kategoris = \App\Models\Kategori::orderBy('nama')->get();

        return view('adminkantin.unit.edit', compact('unit', 'kategoris'));
    }

    // ────────────────────────────────────────────────────────────────
    //  UPDATE  (multiple file upload + hapus dokumen lama jika diminta)
    // ────────────────────────────────────────────────────────────────
    public function update(Request $request, Ruko $unit)
    {
        $validated = $request->validate([
            'kategori_id'       => 'required|exists:kategori,id',
            'status_unit'       => 'required|in:terisi,kosong',
            // Array file baru: opsional
            'dokumen'           => 'nullable|array|max:10',
            'dokumen.*'         => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            // ID dokumen lama yang ingin dihapus (checkbox di view)
            'hapus_dokumen'     => 'nullable|array',
            'hapus_dokumen.*'   => 'integer|exists:dokumentasi_unit,id',
        ]);

        $unit->update([
            'kategori_id' => $validated['kategori_id'],
            'status_unit' => $validated['status_unit'],
        ]);

        // Hapus dokumen lama yang dipilih user + file fisiknya
        if (!empty($validated['hapus_dokumen'])) {
            $dokumenLama = DokumentasiUnit::whereIn('id', $validated['hapus_dokumen'])
                ->where('ruko_id', $unit->id) // pastikan milik unit ini
                ->get();

            foreach ($dokumenLama as $dok) {
                Storage::disk('public')->delete($dok->file);
                $dok->delete();
            }
        }

        // Simpan multiple file dokumentasi baru (jika ada)
        if ($request->hasFile('dokumen')) {
            $this->simpanDokumentasi(
                $unit->id,
                $request->file('dokumen')
            );
        }

        return redirect()
            ->route('adminkantin.unit.index')
            ->with('success', "Unit {$unit->kode_unit} berhasil diperbarui.");
    }

    // ────────────────────────────────────────────────────────────────
    //  DESTROY  (validasi keamanan: hanya bisa hapus jika status = kosong)
    // ────────────────────────────────────────────────────────────────
    public function destroy(Ruko $unit)
    {
        // ── Validasi keamanan bisnis ──────────────────────────────
        if ($unit->status_unit === 'terisi') {
            return back()->with(
                'error',
                "Unit {$unit->kode_unit} tidak dapat dihapus karena sedang berstatus 'Terisi'. " .
                "Pastikan unit sudah dikosongkan terlebih dahulu."
            );
        }

        // Hapus semua file fisik dokumentasi sebelum record-nya dihapus
        foreach ($unit->dokumentasiUnit as $dok) {
            Storage::disk('public')->delete($dok->file);
        }

        // Record dokumentasi_unit akan terhapus otomatis via CASCADE di DB
        $kode = $unit->kode_unit;
        $unit->delete();

        return redirect()
            ->route('adminkantin.unit.index')
            ->with('success', "Unit {$kode} berhasil dihapus.");
    }

    // ────────────────────────────────────────────────────────────────
    //  UPDATE DETAIL DOKUMEN  (AJAX - PATCH)
    // ────────────────────────────────────────────────────────────────
    public function updateDokumenDetail(Request $request, DokumentasiUnit $dokumen)
    {
        $validated = $request->validate([
            'judul_dokumen' => 'required|string|max:150',
            'deskripsi'     => 'nullable|string',
        ]);

        $dokumen->update([
            'judul_dokumen' => $validated['judul_dokumen'],
            'deskripsi'     => $validated['deskripsi'],
        ]);

        return response()->json([
            'success'        => true,
            'judul_dokumen'  => $dokumen->fresh()->judul_dokumen,
            'deskripsi'      => $dokumen->fresh()->deskripsi ?? '-',
            'message'        => 'Detail dokumen berhasil diperbarui.',
        ]);
    }
}
