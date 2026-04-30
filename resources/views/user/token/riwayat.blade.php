@extends('layouts.publik')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 py-2"><i class="fas fa-history mr-2"></i> Riwayat Booking - {{ $bookingFutsal->no_hp }}</h5>
                    <a href="{{ route('user.token.show', $token) }}" class="btn btn-sm btn-light">Kembali ke Detail</a>
                </div>
                <div class="card-body p-4">
                    
                    @if($riwayat->isEmpty())
                        <div class="alert alert-warning text-center">
                            Belum ada riwayat booking.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Lapangan</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayat as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->lapangan->nama }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($item->start_datetime)->locale('id')->translatedFormat('d M Y') }}<br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->start_datetime)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->end_datetime)->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($item->status == 'menunggu')
                                                    <span class="badge badge-warning">Menunggu</span>
                                                @elseif($item->status == 'dikonfirmasi')
                                                    <span class="badge badge-primary">Dikonfirmasi</span>
                                                @elseif($item->status == 'selesai')
                                                    <span class="badge badge-success">Selesai</span>
                                                @elseif($item->status == 'dibatalkan')
                                                    <span class="badge badge-danger">Dibatalkan</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($item->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('user.token.show', $item->access_token) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i> Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
