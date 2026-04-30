@extends('layouts.publik')

@section('title', 'Riwayat Booking - ' . ($user->nama_lengkap ?? $user->name))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Layanan Anda</h1>
        <a href="{{ route('user.token.show', $token) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Detail
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="bg-primary text-white rounded-circle p-3">
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                </div>
                <div class="col">
                    <h5 class="font-weight-bold text-gray-800 mb-1">{{ $user->nama_lengkap ?? $user->name }}</h5>
                    <p class="text-muted mb-0"><i class="fas fa-phone fa-sm mr-1"></i> {{ $user->no_hp }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @php $hasAnyBooking = false; @endphp
        @foreach(['futsal' => 'Futsal', 'ac' => 'Servis AC', 'servis' => 'Servis Kendaraan', 'kantin' => 'Sewa Kantin'] as $key => $title)
        @if(isset($bookings[$key]) && count($bookings[$key]) > 0)
        @php $hasAnyBooking = true; @endphp
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>ID Booking</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings[$key] as $index => $b)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>#{{ $b->id }}</td>
                                    <td>
                                        @if($key == 'futsal') {{ $b->start_datetime->format('d/m/Y') }}
                                        @elseif($key == 'ac') {{ \Carbon\Carbon::parse($b->tgl_kunjungan)->format('d/m/Y') }}
                                        @elseif($key == 'servis') {{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') }}
                                        @elseif($key == 'kantin') {{ \Carbon\Carbon::parse($b->tgl_mulai)->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($key == 'futsal') {{ $b->lapangan->nama ?? '-' }}
                                        @elseif($key == 'ac') {{ $b->layanan->nama ?? '-' }}
                                        @elseif($key == 'servis') {{ $b->nomor_plat }} ({{ $b->layananServis->nama_layanan ?? '-' }})
                                        @elseif($key == 'kantin') Unit {{ $b->ruko->kode_unit ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ ($b->status ?? ($b->booking->status ?? '')) == 'selesai' || ($b->status ?? ($b->booking->status ?? '')) == 'aktif' ? 'success' : (($b->status ?? ($b->booking->status ?? '')) == 'dibatalkan' || ($b->status ?? ($b->booking->status ?? '')) == 'batal' ? 'danger' : 'warning') }}">
                                            {{ strtoupper($b->status ?? ($b->booking->status ?? 'MENUNGGU')) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($b->access_token)
                                            <a href="{{ route('user.token.show', $b->access_token) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach

        @if(!$hasAnyBooking)
        <div class="col-12">
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-2x mb-3 d-block text-info"></i>
                <h6 class="font-weight-bold">Belum ada riwayat layanan apapun.</h6>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
