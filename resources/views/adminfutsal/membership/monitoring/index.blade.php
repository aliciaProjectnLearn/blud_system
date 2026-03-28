{{-- resources/views/adminfutsal/membership/monitoring/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Monitoring Membership</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Statistik --}}
        <div class="row mb-4">
            <div class="col-xl-4 col-md-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Membership</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-id-card fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Membership Aktif</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['aktif'] }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Kuota Habis</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['tidak_aktif'] }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Membership</h6>

                {{-- Filter --}}
                <form method="GET" action="{{ route('adminfutsal.monitoring-membership.index') }}" class="form-inline">
                    <select name="status" class="form-control form-control-sm mr-2">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak aktif" {{ request('status') === 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    <select name="paket_id" class="form-control form-control-sm mr-2">
                        <option value="">-- Semua Paket --</option>
                        @foreach ($pakets as $paket)
                            <option value="{{ $paket->id }}" {{ request('paket_id') == $paket->id ? 'selected' : '' }}>
                                {{ $paket->nama_paket }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                    @if (request()->filled('status') || request()->filled('paket_id'))
                        <a href="{{ route('adminfutsal.monitoring-membership.index') }}" class="btn btn-sm btn-light ml-1">Reset</a>
                    @endif
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Paket</th>
                                <th>Total Kuota</th>
                                <th>Sisa Kuota</th>
                                <th>Status</th>
                                <th>Tanggal Beli</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($memberships as $i => $m)
                                <tr>
                                    <td>{{ $memberships->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $m->user->name ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $m->user->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $m->paket->nama_paket ?? '-' }}</td>
                                    <td class="text-center">{{ $m->total_kuota }}x</td>
                                    <td class="text-center">
                                        <span class="font-weight-bold {{ $m->sisa_kuota === 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $m->sisa_kuota }}x
                                        </span>
                                    </td>
                                    <td>
                                        @if ($m->status === 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>{{ $m->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data membership.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    {{ $memberships->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
