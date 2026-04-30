@extends('layouts.app')

@section('title', 'Manajemen Booking Servis')

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
    <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Manajemen Booking Servis</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.servis.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Booking</li>
        </ol>
    </nav>
</div>

<!-- Slot Availability Summary -->
@include('adminservis.booking.partials.slot_summary')

<!-- Table Section -->
<div class="card shadow mb-4" x-data="{ search: '{{ request('search') }}' }">
    <div class="card-header py-3">
        <form action="{{ route('admin.servis.booking.index') }}" method="GET" class="row gx-3 gy-2 align-items-center">
            <div class="col-md-3">
                <label class="sr-only" for="search">Search</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-search"></i></div>
                    </div>
                    <input type="text" name="search" class="form-control" id="search" placeholder="Nama pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="sr-only" for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" id="tanggal" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-2">
                <label class="sr-only" for="status">Status</label>
                <select name="status" class="form-control" id="status">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.servis.booking.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th>Teknisi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-gray-800">{{ $booking->user->nama_lengkap ?? $booking->user->name }}</div>
                                <div class="small text-muted">{{ $booking->user->no_hp ?? '-' }}</div>
                                <div class="mt-1">
                                    <span class="badge badge-secondary py-1 px-2">{{ strtoupper($booking->tipe_kendaraan) }}</span>
                                    <code class="ml-1">{{ $booking->nomor_plat }}</code>
                                </div>
                            </td>
                            <td>{{ $booking->tanggal_booking->translatedFormat('d M Y') }}</td>
                            <td class="font-weight-bold">{{ \Carbon\Carbon::parse($booking->jam_booking)->format('H:i') }}</td>
                            <td>
                                @php
                                    $status = $booking->status;
                                    $badge = 'secondary';
                                    if($status == 'menunggu') $badge = 'warning';
                                    elseif($status == 'diproses') $badge = 'primary';
                                    elseif($status == 'selesai') $badge = 'success';
                                    elseif($status == 'batal') $badge = 'danger';
                                @endphp
                                <span class="badge badge-{{ $badge }} px-3 py-2 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    {{ $status }}
                                </span>
                            </td>
                            <td>
                                @if($booking->teknisi)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-gray-200 rounded-circle text-center mr-2" style="width: 30px; height: 30px; line-height: 30px;">
                                            <i class="fas fa-user-cog text-gray-500"></i>
                                        </div>
                                        <div class="text-sm font-weight-bold">{{ $booking->teknisi->name }}</div>
                                    </div>
                                @else
                                    <span class="text-muted italic"><i class="fas fa-user-slash mr-1"></i> Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.servis.booking.show', $booking->id) }}" class="btn btn-info btn-sm btn-circle shadow-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 bg-light">
                                <i class="fas fa-calendar-times mb-2 fa-2x text-gray-300"></i>
                                <p class="mb-0 text-gray-500 italic">Data booking tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
