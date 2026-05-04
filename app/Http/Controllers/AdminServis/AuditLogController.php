<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::where('sistem', 'servis')
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

        // Dropdown filter — hanya untuk sistem servis
        $daftarAksi = AuditLog::where('sistem', 'servis')
            ->distinct()->pluck('aksi');

        $daftarAdmin = \App\Models\User::whereIn('id', 
            AuditLog::where('sistem', 'servis')
                ->pluck('dilakukan_oleh')
                ->filter()
        )->get(['id', 'name']);

        return view('adminservis.audit.index', 
            compact('logs', 'daftarAksi', 'daftarAdmin'));
    }

    public function show($id)
    {
        $log = AuditLog::where('sistem', 'servis')->findOrFail($id);
        return view('adminservis.audit.show', compact('log'));
    }
}
