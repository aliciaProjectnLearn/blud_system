@extends('layouts.app')

@section('title', 'Manajemen Review / Testimoni')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Review / Testimoni</h1>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4 border-left-primary">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-star mr-2"></i>Daftar Review Pelanggan</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4">
            <i class="fas fa-info-circle mr-2"></i><strong>Info:</strong> Review yang berstatus <strong>Di-ACC</strong> akan secara otomatis ditampilkan di halaman utama (Landing Page) bagian "Kata Mereka". Hanya 5 review terbaru yang akan ditampilkan.
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="10%">Tanggal</th>
                        <th width="15%">Pelanggan</th>
                        <th width="15%">Rating</th>
                        <th>Isi Review</th>
                        <th width="10%">Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($testimonials as $t)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="font-weight-bold">{{ $t->user->name ?? 'Pengguna' }}</td>
                            <td>
                                <div class="text-warning">
                                    @for($i = 0; $i < $t->rating; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                    @for($i = $t->rating; $i < 5; $i++)
                                        <i class="far fa-star"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="text-left text-gray-700" style="word-wrap: break-word;">
                                <i>"{{ $t->content }}"</i>
                            </td>
                            <td>
                                @if($t->status === 'pending')
                                    <span class="badge badge-warning px-2 py-1">Pending</span>
                                @elseif($t->status === 'approved')
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Di-ACC</span>
                                    <div class="small mt-1 text-muted" style="font-size: 10px;">Oleh: {{ $t->approver->name ?? '-' }}</div>
                                @else
                                    <span class="badge badge-danger px-2 py-1">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status !== 'approved')
                                    <form action="{{ route('admin.testimonial.updateStatus', $t->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-success shadow-sm" onclick="return confirm('ACC review ini agar tampil di halaman utama?')" title="Setujui">
                                            <i class="fas fa-check"></i> ACC
                                        </button>
                                    </form>
                                @endif
                                
                                @if($t->status !== 'rejected')
                                    <form action="{{ route('admin.testimonial.updateStatus', $t->id) }}" method="POST" class="d-inline-block mt-1 mt-md-0">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Tolak/Sembunyikan review ini?')" title="Tolak">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="far fa-sad-tear fa-3x mb-3 text-gray-300 d-block"></i>
                                Belum ada review dari pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $testimonials->links() }}
        </div>
    </div>
</div>
@endsection
