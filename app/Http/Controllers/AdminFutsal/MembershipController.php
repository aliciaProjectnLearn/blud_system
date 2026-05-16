<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\PaketMembership;
use App\Models\User;
use App\Services\MembershipService;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function __construct(protected MembershipService $membershipService) {}

    public function index(Request $request)
    {
        $query = Membership::with(['user', 'paket', 'transaksi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('paket_id')) {
            $query->where('paket_membership_id', $request->paket_id);
        }

        $memberships = $query->latest()->paginate(15);
        $pakets      = PaketMembership::where('status', 'aktif')->get();

        $stats = [
            'total'       => Membership::count(),
            'aktif'       => Membership::where('status', 'aktif')->count(),
            'tidak_aktif' => Membership::where('status', 'tidak aktif')->count(),
        ];

        return view('adminfutsal.membership.monitoring.index', compact('memberships', 'pakets', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'paket_id'  => 'required|exists:paket_membership,id',
            'transaksi_id' => 'required|integer',
        ]);

        try {
            $this->membershipService->buat(
                $request->user_id,
                $request->paket_id,
                $request->transaksi_id
            );

            return redirect()->route('adminfutsal.monitoring-membership.index')
                ->with('success', 'Membership berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
