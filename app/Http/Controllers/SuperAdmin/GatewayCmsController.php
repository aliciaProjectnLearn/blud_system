<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsGateway;
use App\Models\CmsKeunggulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GatewayCmsController extends Controller
{
    public function index()
    {
        $cms = CmsGateway::orderBy('urutan')->get()->groupBy('grup');
        $keunggulan = CmsKeunggulan::ordered()->get();
        $nextUrutan = CmsKeunggulan::max('urutan') + 1;
        if (!$nextUrutan) {
            $nextUrutan = 1;
        }
        
        return view('dashboard.cms.gateway.index', compact('cms', 'keunggulan', 'nextUrutan'));
    }

    public function updateKonten(Request $request)
    {
        $konten = $request->input('konten', []);
        
        try {
            DB::beginTransaction();

            foreach ($konten as $key => $value) {
                CmsGateway::where('key', $key)->update(['value' => $value]);
            }

            DB::commit();
            return redirect()->route('admin.cms.gateway.index')->with('success', 'Konten Gateway berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error GatewayCmsController@updateKonten: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan konten.');
        }
    }

    public function storeKeunggulan(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'icon_svg'  => 'required|string',
            'is_active' => 'nullable',
            'urutan'    => 'nullable|integer|min:0',
        ]);

        try {
            $nextUrutan = CmsKeunggulan::max('urutan') + 1;
            CmsKeunggulan::create([
                'judul'     => $request->judul,
                'deskripsi' => $request->deskripsi,
                'icon_svg'  => $request->icon_svg,
                'urutan'    => $request->urutan ?? ($nextUrutan ?: 1),
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.cms.gateway.index')->with('success', 'Keunggulan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error GatewayCmsController@storeKeunggulan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan keunggulan.');
        }
    }

    public function updateKeunggulan(Request $request, $id)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'icon_svg'  => 'required|string',
            'is_active' => 'nullable',
            'urutan'    => 'nullable|integer|min:0',
        ]);

        try {
            $keunggulan = CmsKeunggulan::findOrFail($id);
            $keunggulan->update([
                'judul'     => $request->judul,
                'deskripsi' => $request->deskripsi,
                'icon_svg'  => $request->icon_svg,
                'urutan'    => $request->urutan ?? $keunggulan->urutan ?? 0,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.cms.gateway.index')->with('success', 'Keunggulan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error GatewayCmsController@updateKeunggulan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui keunggulan.');
        }
    }

    public function destroyKeunggulan($id)
    {
        try {
            $keunggulan = CmsKeunggulan::findOrFail($id);
            $keunggulan->delete();
            return redirect()->route('admin.cms.gateway.index')->with('success', 'Keunggulan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error GatewayCmsController@destroyKeunggulan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus keunggulan.');
        }
    }

    public function toggleKeunggulan($id)
    {
        try {
            $keunggulan = CmsKeunggulan::findOrFail($id);
            $keunggulan->is_active = !$keunggulan->is_active;
            $keunggulan->save();

            return response()->json([
                'success' => true,
                'is_active' => $keunggulan->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan.'], 500);
        }
    }

    public function updateUrutanKeunggulan(Request $request)
    {
        $urutanArray = $request->input('urutan'); 

        if (!is_array($urutanArray)) {
            return response()->json(['success' => false], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($urutanArray as $item) {
                CmsKeunggulan::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error GatewayCmsController@updateUrutanKeunggulan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah urutan'], 500);
        }
    }
}
