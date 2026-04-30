@extends('layouts.app')

@section('title', 'Manajemen Pelanggan AC')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Pelanggan AC</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan Aktif</h6>
        <form action="{{ route('admin.ac.pelanggan.index') }}" method="GET" class="form-inline">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
            @if(request()->has('search') && request('search') != '')
                <a href="{{ route('admin.ac.pelanggan.index') }}" class="btn btn-secondary btn-sm ml-2">Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Pelanggan</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th class="text-center">Total Booking</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggans as $index => $pelanggan)
                        <tr>
                            <td class="text-center">{{ $pelanggans->firstItem() + $index }}</td>
                            <td>
                                <div class="font-weight-bold text-gray-800">{{ $pelanggan->name }}</div>
                            </td>
                            <td>{{ $pelanggan->email }}</td>
                            <td>{{ $pelanggan->no_hp ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-primary px-3">
                                    {{ $pelanggan->booking_ac_count }} Pesanan
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.ac.pelanggan.show', $pelanggan->id) }}" class="btn btn-info btn-sm shadow-sm">
                                    <i class="fas fa-history mr-1"></i> Riwayat Layanan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 d-block"></i>
                                Pelanggan tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $pelanggans->links() }}
        </div>
    </div>
</div>
@endsection
