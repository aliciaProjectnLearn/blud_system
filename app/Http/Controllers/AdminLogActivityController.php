<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use Illuminate\Http\Request;

class AdminLogActivityController extends Controller
{
    public function index(Request $request)
    {
        $logs = LogActivity::with('user')
            ->when($request->sistem, fn($q, $sistem) => $q->where('sistem', $sistem))
            ->when($request->search, function($q, $search) {
                $q->where(function($q) use ($search) {
                    $q->where('nama_user', 'like', "%{$search}%")
                      ->orWhere('deskripsi_aktivitas', 'like', "%{$search}%");
                });
            })
            ->when($request->start_date && $request->end_date, fn($q) => $q->whereBetween('created_at', [$request->start_date, $request->end_date]))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return view('dashboard.monitoring', compact('logs'));
    }
}
