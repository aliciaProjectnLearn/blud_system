<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        // Load with user and approver relations
        $testimonials = Testimonial::with(['user', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('superadmin.testimonial.index', compact('testimonials'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $pesan = $request->status === 'approved' 
            ? 'Testimoni berhasil di-ACC dan akan ditampilkan di halaman utama.' 
            : 'Testimoni berhasil disembunyikan/ditolak.';
        
        return back()->with('success', $pesan);
    }
}
