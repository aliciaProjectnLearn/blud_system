<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use App\Models\PaketMembership;
use Illuminate\Http\Request;

class PaketMembershipController extends Controller
{
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

        return redirect()->route('adminfutsal.paket-membership.index')
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

        return redirect()->route('adminfutsal.paket-membership.index')
            ->with('success', 'Paket membership berhasil diupdate.');
    }

    public function destroy(PaketMembership $paketMembership)
    {
        // Cegah hapus paket yang masih dipakai
        if ($paketMembership->memberships()->exists()) {
            return redirect()->route('adminfutsal.paket-membership.index')
                ->with('error', 'Paket tidak bisa dihapus karena masih digunakan.');
        }

        $paketMembership->delete();

        return redirect()->route('adminfutsal.paket-membership.index')
            ->with('success', 'Paket membership berhasil dihapus.');
    }
}
