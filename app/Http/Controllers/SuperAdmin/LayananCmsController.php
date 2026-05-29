<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LayananCmsController extends Controller
{
    public function index()
    {
        $layanans = Layanan::ordered()->get()->map(function($layanan) {
            $slug      = Str::slug($layanan->nama_layanan);
            $roleName  = 'Admin' . Str::studly($slug);
            $role      = \App\Models\Role::where('nama', $roleName)->first();
            
            $layanan->role_id      = $role?->id;
            $layanan->role_nama    = $roleName;
            $layanan->jumlah_admin = $role 
                ? \App\Models\User::whereHas('roles', function($q) use ($role) {
                    $q->where('roles.id', $role->id);
                  })->count()
                : 0;
            
            return $layanan;
        });

        return view('dashboard.cms.layanan.index', compact('layanans'));
    }

    public function create()
    {
        $nextUrutan = Layanan::max('urutan') + 1;
        return view('dashboard.cms.layanan.create', compact('nextUrutan'));
    }

    public function edit($id)
    {
        $layanan = Layanan::findOrFail($id);
        return view('dashboard.cms.layanan.edit', compact('layanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:100',
            'deskripsi'    => 'required|string',
            'icon_class'   => 'required|string',
            'url'          => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Format URL tidak valid. Pastikan diawali https://');
                    }
                },
            ],
            'is_active'    => 'nullable',
            'urutan'       => 'nullable|integer|min:0',
        ]);

        // Generate slug dari nama layanan
        $slug = Str::slug($request->nama_layanan);

        // Auto-generate route_name
        $routeName = 'user.' . $slug . '.index';

        try {
            DB::beginTransaction();

            $nextUrutan = Layanan::max('urutan') + 1;

            $layanan = Layanan::create([
                'nama_layanan' => $request->nama_layanan,
                'deskripsi'    => $request->deskripsi,
                'icon_class'   => $request->icon_class,
                'icon_svg'     => '',
                'route_name'   => $routeName,
                'url'          => $request->url,
                'is_active'    => $request->has('is_active') ? true : false,
                'urutan'       => $request->urutan ?: $nextUrutan,
            ]);

            // Auto-generate files
            $this->generateLayananFiles($slug, $request->nama_layanan);

            // Generate nama role dari nama layanan
            // Contoh: "Kolam Renang" → "AdminKolamRenang"
            $roleName = 'Admin' . Str::studly($slug);

            // Buat role baru jika belum ada
            $role = \App\Models\Role::firstOrCreate(
                ['nama' => $roleName]
            );

            DB::commit();

            return redirect()
                ->route('admin.cms.layanan.index')
                ->with('success', "Layanan '{$request->nama_layanan}' berhasil ditambahkan. File controller & view telah di-generate.")
                ->with('generated_files', $this->getGeneratedFilesList($slug))
                ->with('new_layanan_nama', $request->nama_layanan)
                ->with('new_role_id', $role->id)
                ->with('new_role_nama', $roleName)
                ->with('show_create_admin_prompt', true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LayananCmsController@store: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:100',
            'deskripsi'    => 'required|string',
            'icon_class'   => 'required|string',
            'url'          => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Format URL tidak valid. Pastikan diawali https://');
                    }
                },
            ],
            'is_active'    => 'nullable',
            'urutan'       => 'nullable|integer|min:0',
        ]);

        $layanan = Layanan::findOrFail($id);

        try {
            DB::beginTransaction();

            $layanan->update([
                'nama_layanan' => $request->nama_layanan,
                'deskripsi'    => $request->deskripsi,
                'icon_class'   => $request->icon_class,
                'url'          => $request->url,
                'is_active'    => $request->has('is_active') ? true : false,
                'urutan'       => $request->urutan ?? 0,
            ]);

            DB::commit();
            return redirect()->route('admin.cms.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error LayananCmsController@update: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);
        $slug    = Str::slug($layanan->nama_layanan);
        $tables  = $this->getRelatedTablesBySlug($slug);

        // Cek apakah ada data di tabel-tabel terkait
        $adaData = false;
        $tabelBerdata = [];

        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                if ($count > 0) {
                    $adaData = true;
                    $tabelBerdata[] = $table . " ({$count} data)";
                }
            } catch (\Exception $e) {
                // Tabel tidak ada, skip
                Log::warning("Tabel {$table} tidak ditemukan saat cek hapus layanan.");
            }
        }

        if ($adaData) {
            $pesanTabel = implode(', ', $tabelBerdata);
            return back()->with('error', 
                "Layanan '{$layanan->nama_layanan}' tidak dapat dihapus karena " .
                "masih memiliki data di: {$pesanTabel}. " .
                "Nonaktifkan layanan jika tidak ingin ditampilkan."
            );
        }

        try {
            // Hapus semua file yang berkaitan
            $deletedFiles = $this->deleteLayananFiles($slug);

            // Hapus data dari database
            $layanan->delete();

            $pesanFile = count($deletedFiles) > 0
                ? ' File terkait (' . count($deletedFiles) . ' file) ikut dihapus.'
                : ' Tidak ada file generate yang ditemukan.';

            return redirect()
                ->route('admin.cms.layanan.index')
                ->with('success', 
                    "Layanan '{$layanan->nama_layanan}' berhasil dihapus.{$pesanFile}"
                )
                ->with('deleted_files', $deletedFiles);

        } catch (\Exception $e) {
            Log::error('LayananCmsController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus layanan.');
        }
    }

    public function toggleActive($id)
    {
        try {
            $layanan = Layanan::findOrFail($id);
            $layanan->is_active = !$layanan->is_active;
            $layanan->save();

            return response()->json([
                'success' => true,
                'is_active' => $layanan->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan.'
            ], 500);
        }
    }

    public function updateUrutan(Request $request)
    {
        $urutanArray = $request->input('urutan'); 

        if (!is_array($urutanArray)) {
            return response()->json(['success' => false], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($urutanArray as $item) {
                Layanan::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error LayananCmsController@updateUrutan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah urutan'], 500);
        }
    }

    private function getRelatedTablesBySlug(string $slug): array
    {
        // Mapping slug layanan ke tabel yang perlu dicek
        $mapping = [
            'futsal'   => ['booking_futsals', 'pembayaran_futsals'],
            'ac'       => ['booking_acs', 'pembayaran_acs'],
            'servis'   => ['booking_servis', 'pembayaran_servis'],
            'kantin'   => ['bookings', 'sewa_rukos', 'pembayaran_rukos'],
        ];

        // Cek exact match dulu
        if (isset($mapping[$slug])) {
            return $mapping[$slug];
        }

        // Cek partial match
        foreach ($mapping as $key => $tables) {
            if (str_contains($slug, $key)) {
                return $tables;
            }
        }

        return [];
    }

    private function deleteLayananFiles(string $slug): array
    {
        $studly  = Str::studly($slug);
        $deleted = [];

        // 1. User Controller
        $userControllerPath = app_path(
            "Http/Controllers/User/{$studly}Controller.php"
        );
        if (file_exists($userControllerPath)) {
            File::delete($userControllerPath);
            $deleted[] = "app/Http/Controllers/User/{$studly}Controller.php";
        }

        // 2. Admin Controller directory
        $adminControllerDir = app_path(
            "Http/Controllers/Admin{$studly}"
        );
        if (is_dir($adminControllerDir)) {
            File::deleteDirectory($adminControllerDir);
            $deleted[] = "app/Http/Controllers/Admin{$studly}/ (direktori)";
        }

        // 3. User View directory
        $userViewDir = resource_path("views/user/{$slug}");
        if (is_dir($userViewDir)) {
            File::deleteDirectory($userViewDir);
            $deleted[] = "resources/views/user/{$slug}/ (direktori)";
        }

        // 4. Admin View directory
        $adminViewDir = resource_path("views/admin{$slug}");
        if (is_dir($adminViewDir)) {
            File::deleteDirectory($adminViewDir);
            $deleted[] = "resources/views/admin{$slug}/ (direktori)";
        }

        // 5. Hapus blok route AUTO-GENERATED dari web.php
        $webPhpPath = base_path('routes/web.php');
        if (file_exists($webPhpPath)) {
            $webContent = File::get($webPhpPath);
            $namaLayanan = $this->getNamaLayananBySlug($slug);

            $pattern = '/\n*\/\/ ===== AUTO-GENERATED: [^\n]*' 
                . preg_quote($slug, '/') 
                . '[^\n]*=====.*?\/\/ ===== END AUTO-GENERATED: [^\n]*'
                . preg_quote($slug, '/')
                . '[^\n]*=====\n*/s';

            $newContent = preg_replace($pattern, PHP_EOL, $webContent);

            if ($newContent !== $webContent) {
                File::put($webPhpPath, $newContent);
                $deleted[] = "routes/web.php (blok AUTO-GENERATED dihapus)";
            }
        }

        return $deleted;
    }

    private function getNamaLayananBySlug(string $slug): string
    {
        return $slug;
    }

    private function generateLayananFiles(string $slug, string $namaLayanan): void
    {
        $studly = Str::studly($slug);      // servis-motor -> ServisMotor
        $camel  = Str::camel($slug);       // servis-motor -> servisMotor

        // 1. User Controller
        $userControllerPath = app_path("Http/Controllers/User/{$studly}Controller.php");
        $userControllerContent = '<?php' . PHP_EOL . PHP_EOL
            . 'namespace App\Http\Controllers\User;' . PHP_EOL . PHP_EOL
            . 'use App\Http\Controllers\Controller;' . PHP_EOL
            . 'use Illuminate\Http\Request;' . PHP_EOL . PHP_EOL
            . "class {$studly}Controller extends Controller" . PHP_EOL
            . '{' . PHP_EOL
            . '    public function index()' . PHP_EOL
            . '    {' . PHP_EOL
            . "        return view('user.{$slug}.index');" . PHP_EOL
            . '    }' . PHP_EOL
            . '}' . PHP_EOL;

        if (!file_exists($userControllerPath)) {
            File::put($userControllerPath, $userControllerContent);
        }

        // 2. Admin Controller
        $adminControllerDir  = app_path("Http/Controllers/Admin{$studly}");
        $adminControllerPath = "{$adminControllerDir}/DashboardController.php";
        $adminControllerContent = '<?php' . PHP_EOL . PHP_EOL
            . "namespace App\Http\Controllers\Admin{$studly};" . PHP_EOL . PHP_EOL
            . 'use App\Http\Controllers\Controller;' . PHP_EOL
            . 'use Illuminate\Http\Request;' . PHP_EOL . PHP_EOL
            . 'class DashboardController extends Controller' . PHP_EOL
            . '{' . PHP_EOL
            . '    public function index()' . PHP_EOL
            . '    {' . PHP_EOL
            . "        return view('admin{$slug}.index');" . PHP_EOL
            . '    }' . PHP_EOL
            . '}' . PHP_EOL;

        if (!is_dir($adminControllerDir)) {
            File::makeDirectory($adminControllerDir, 0755, true);
        }
        if (!file_exists($adminControllerPath)) {
            File::put($adminControllerPath, $adminControllerContent);
        }

        // 3. User View
        $userViewDir  = resource_path("views/user/{$slug}");
        $userViewPath = "{$userViewDir}/index.blade.php";
        $userViewContent = "@extends('layouts.publik')" . PHP_EOL . PHP_EOL
            . "@section('title', '{$namaLayanan}')" . PHP_EOL . PHP_EOL
            . "@section('content')" . PHP_EOL
            . '<div class="container py-5">' . PHP_EOL
            . "    <h1>{$namaLayanan}</h1>" . PHP_EOL
            . '    <p>Halaman ini belum dikonfigurasi. Silakan hubungi developer.</p>' . PHP_EOL
            . '</div>' . PHP_EOL
            . '@endsection' . PHP_EOL;

        if (!is_dir($userViewDir)) {
            File::makeDirectory($userViewDir, 0755, true);
        }
        if (!file_exists($userViewPath)) {
            File::put($userViewPath, $userViewContent);
        }

        // 4. Admin View
        $adminViewDir  = resource_path("views/admin{$slug}");
        $adminViewPath = "{$adminViewDir}/index.blade.php";
        $adminViewContent = "@extends('layouts.app')" . PHP_EOL . PHP_EOL
            . "@section('title', 'Dashboard {$namaLayanan}')" . PHP_EOL . PHP_EOL
            . "@section('content')" . PHP_EOL
            . '<div class="d-sm-flex align-items-center justify-content-between mb-4">' . PHP_EOL
            . "    <h1 class=\"h3 mb-0 text-gray-800\">Dashboard {$namaLayanan}</h1>" . PHP_EOL
            . '</div>' . PHP_EOL
            . '<div class="alert alert-info">' . PHP_EOL
            . '    Modul ini belum dikonfigurasi. Silakan hubungi developer.' . PHP_EOL
            . '</div>' . PHP_EOL
            . '@endsection' . PHP_EOL;

        if (!is_dir($adminViewDir)) {
            File::makeDirectory($adminViewDir, 0755, true);
        }
        if (!file_exists($adminViewPath)) {
            File::put($adminViewPath, $adminViewContent);
        }

        // 5. Tambahkan route ke web.php
        $this->appendRoutesToWebPhp($slug, $studly, $namaLayanan);
    }

    private function appendRoutesToWebPhp(
        string $slug, 
        string $studly, 
        string $namaLayanan
    ): void {
        $webPhpPath = base_path('routes/web.php');
        $routeBlock = PHP_EOL . PHP_EOL
            . "// ===== AUTO-GENERATED: {$namaLayanan} =====" . PHP_EOL
            . '// User Route' . PHP_EOL
            . "Route::prefix('{$slug}')->name('user.{$slug}.')" . PHP_EOL
            . "    ->group(function () {" . PHP_EOL
            . "    Route::get('/', [App\\Http\\Controllers\\User\\{$studly}Controller::class, 'index'])" . PHP_EOL
            . "        ->name('index');" . PHP_EOL
            . '});' . PHP_EOL . PHP_EOL
            . '// Admin Route' . PHP_EOL
            . "Route::middleware(['auth', 'role:Superadmin'])->prefix('admin/{$slug}')->name('admin.{$slug}.')" . PHP_EOL
            . "    ->group(function () {" . PHP_EOL
            . "    Route::get('/dashboard', [App\\Http\\Controllers\\Admin{$studly}\\DashboardController::class, 'index'])" . PHP_EOL
            . "        ->name('dashboard');" . PHP_EOL
            . '});' . PHP_EOL
            . "// ===== END AUTO-GENERATED: {$namaLayanan} =====" . PHP_EOL;

        // Append ke akhir web.php
        File::append($webPhpPath, $routeBlock);
    }

    private function getGeneratedFilesList(string $slug): array
    {
        $studly = Str::studly($slug);
        return [
            "app/Http/Controllers/User/{$studly}Controller.php",
            "app/Http/Controllers/Admin{$studly}/DashboardController.php",
            "resources/views/user/{$slug}/index.blade.php",
            "resources/views/admin{$slug}/index.blade.php",
            "routes/web.php (ditambahkan route baru di bagian bawah)",
        ];
    }
}
