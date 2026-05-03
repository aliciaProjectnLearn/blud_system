<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::where('sistem', 'kantin')
            ->latest('created_at');

        // Filter tanggal
        if ($request->dari) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->sampai) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        // Filter aksi
        if ($request->aksi) {
            $query->where('aksi', $request->aksi);
        }

        // Filter pelaku
        if ($request->pelaku) {
            $query->where('dilakukan_oleh', $request->pelaku);
        }

        $perPage = in_array($request->per_page, [10, 20, 50, 100])
                   ? $request->per_page : 20;

        $logs = $query->paginate($perPage)->withQueryString();

        // Dropdown filter — hanya untuk sistem kantin
        $daftarAksi = AuditLog::where('sistem', 'kantin')
            ->distinct()->pluck('aksi');

        $daftarAdmin = \App\Models\User::whereIn('id', 
            AuditLog::where('sistem', 'kantin')
                ->pluck('dilakukan_oleh')
                ->filter()
        )->get(['id', 'name']);

        return view('adminkantin.audit.index', 
            compact('logs', 'daftarAksi', 'daftarAdmin'));
    }

    public function show($id)
    {
        $log = AuditLog::where('sistem', 'kantin')->findOrFail($id);
        return view('adminkantin.audit.show', compact('log'));
    }
}
