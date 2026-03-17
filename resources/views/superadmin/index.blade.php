
@extends('layouts.app') <!-- sesuaikan layout -->

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Monitoring Aktivitas</h1>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.logs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label>Filter Sistem</label>
                    <select name="sistem" class="form-control">
                        <option value="">Semua Sistem</option>
                        @foreach($sistemList as $sistem)
                            <option value="{{ $sistem }}" {{ request('sistem') == $sistem ? 'selected' : '' }}>
                                {{ $sistem }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label>Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama user / aktivitas" value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Log -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama User</th>
                            <th>Sistem</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                            <th>Waktu Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>
                                <td>{{ $log->nama_user }}</td>
                                <td>{{ $log->sistem }}</td>
                                <td>{{ $log->aktivitas }}</td>
                                <td>{{ $log->deskripsi_aktivitas }}</td>
                                <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data aktivitas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data
                </div>
                <div>
                    <form method="GET" action="{{ route('admin.logs.index') }}" class="form-inline">
                        <label for="per_page" class="mr-2">Tampilkan</label>
                        <select name="per_page" id="per_page" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="5" {{ request('per_page')==5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page')==10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page')==25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page')==50 ? 'selected' : '' }}>50</option>
                        </select>
                        @foreach(request()->except('per_page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection