@extends('layouts.app')

@section('title', 'Daftar Booking Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Booking Futsal</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-none" id="flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-none" id="flash-error">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Booking Hari Ini & Pending</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Tgl & Jam Main</th>
                            <th>Lapangan</th>
                            <th>Pemesan</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $key => $item)
                            <tr>
                                <td>{{ $bookings->firstItem() + $key }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->start_datetime)->format('d M Y, H:i') }}</td>
                                <td>{{ $item->lapangan->nama ?? '-' }}</td>
                                <td>{{ $item->nama_pemesan ?? ($item->user->name ?? '-') }}</td>
                                <td>
                                    @if($item->jenis_pembayaran == 'paket')
                                        <span class="badge badge-info">Paket</span>
                                    @else
                                        <span class="badge badge-primary">Reguler</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == 'menunggu')
                                        <span class="badge badge-warning">Menunggu</span>
                                    @elseif ($item->status == 'dikonfirmasi')
                                        <span class="badge badge-primary">Dikonfirmasi</span>
                                    @elseif ($item->status == 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kasirfutsal.booking.show', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('flash-success')) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: document.getElementById('flash-success').innerText,
                showConfirmButton: false,
                timer: 3000
            });
        }
        if (document.getElementById('flash-error')) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: document.getElementById('flash-error').innerText,
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
</script>
@endpush
