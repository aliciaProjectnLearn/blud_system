<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use App\Models\PaketMembership;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class PaketMembershipController extends Controller
{
    use Loggable;

    public function index()
    {
        $pakets = PaketMembership::withCount('memberships')->get();

        $stats = [
            'total_paket'    => $pakets->count(),
            'total_pengguna' => $pakets->sum('memberships_count'),
        ];

        return view('adminfutsal.membership.paket.index', compact('pakets', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket'   => 'required|string|max:100',
            'jumlah_kuota' => 'required|integer|min:1',
            'harga'        => 'required|numeric|min:0',
            'status'       => 'required|in:aktif,tidak aktif',
        ]);

        PaketMembership::create($request->only('nama_paket', 'jumlah_kuota', 'harga', 'status'));

        $this->function_log('Futsal', 'create', 'Admin Futsal menambahkan paket membership baru: ' . $request->nama_paket);

        return redirect()->route('admin.futsal.paket-membership.index')
            ->with('success', 'Paket membership berhasil ditambahkan.');
    }

    public function update(Request $request, PaketMembership $paketMembership)
    {
        $request->validate([
            'nama_paket'   => 'required|string|max:100',
            'jumlah_kuota' => 'required|integer|min:1',
            'harga'        => 'required|numeric|min:0',
            'status'       => 'required|in:aktif,tidak aktif',
        ]);

        $paketMembership->update($request->only('nama_paket', 'jumlah_kuota', 'harga', 'status'));

        $this->function_log('Futsal', 'update', 'Admin Futsal mengubah paket membership: ' . $paketMembership->nama_paket);

        return redirect()->route('admin.futsal.paket-membership.index')
            ->with('success', 'Paket membership berhasil diupdate.');
    }

    public function destroy(PaketMembership $paketMembership)
    {
        if ($paketMembership->memberships()->exists()) {
            return redirect()->route('admin.futsal.paket-membership.index')
                ->with('error', 'Paket tidak bisa dihapus karena masih digunakan.');
        }

        $namaPaket = $paketMembership->nama_paket;
        $paketMembership->delete();

        $this->function_log('Futsal', 'delete', 'Admin Futsal menghapus paket membership: ' . $namaPaket);

        return redirect()->route('admin.futsal.paket-membership.index')
            ->with('success', 'Paket membership berhasil dihapus.');
    }
}