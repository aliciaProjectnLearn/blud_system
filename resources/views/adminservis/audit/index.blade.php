@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-weight-bold mb-0 text-gray-800">
            <i class="fas fa-history mr-2 text-primary"></i> Audit Log Servis
        </h4>
        <small class="text-muted">
            Rekaman otomatis seluruh aktivitas sistem servis
        </small>
    </div>

    {{-- Filter --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control form-control-sm"
                           value="{{ request('dari') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control form-control-sm"
                           value="{{ request('sampai') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Jenis Aksi</label>
                    <select name="aksi" class="form-control form-control-sm">
                        <option value="">Semua Aksi</option>
                        @foreach($daftarAksi as $aksi)
                        <option value="{{ $aksi }}" 
                                {{ request('aksi') == $aksi ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $aksi)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Admin/Pelaku</label>
                    <select name="pelaku" class="form-control form-control-sm">
                        <option value="">Semua</option>
                        @foreach($daftarAdmin as $admin)
                        <option value="{{ $admin->id }}"
                                {{ request('pelaku') == $admin->id ? 'selected' : '' }}>
                            {{ $admin->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Per Halaman</label>
                    <select name="per_page" class="form-control form-control-sm"
                            onchange="this.form.submit()">
                        <option value="10"  {{ request('per_page', 20) == 10  ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ request('per_page', 20) == 20  ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ request('per_page', 20) == 50  ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-2 flex-fill">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.servis.audit.index') }}" 
                       class="btn btn-secondary btn-sm ml-1 px-2 flex-fill text-center">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Log --}}
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="pl-4">Waktu</th>
                            <th>Aksi</th>
                            <th>Entitas</th>
                            <th>Pelaku</th>
                            <th>Keterangan</th>
                            <th class="text-center pr-4">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="small text-nowrap pl-4">
                                {{ $log->created_at->format('d/m/Y') }}<br>
                                <span class="text-muted">
                                    {{ $log->created_at->format('H:i:s') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $log->badge_color }} px-2 py-1">
                                    {{ $log->label_aksi }}
                                </span>
                            </td>
                            <td class="small">
                                <span class="text-muted text-uppercase" style="font-size: 10px">{{ $log->tabel_entitas }}</span>
                                @if($log->entitas_id)
                                <br><span class="font-weight-bold text-primary">#{{ $log->entitas_id }}</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($log->tipe_pelaku === 'sistem')
                                    <span class="badge badge-light border text-muted">
                                        <i class="fas fa-robot mr-1"></i>Sistem
                                    </span>
                                @else
                                    <div class="font-weight-bold text-dark">
                                        <i class="fas fa-user-shield mr-1 text-primary"></i>
                                        {{ $log->nama_pelaku ?? '-' }}
                                    </div>
                                    <div class="text-muted" style="font-size: 10px">IP: {{ $log->ip_address }}</div>
                                @endif
                            </td>
                            <td class="small text-muted" style="max-width: 250px;">
                                {{ $log->keterangan ?? '-' }}
                            </td>
                            <td class="text-center pr-4">
                                @if($log->data_lama || $log->data_baru)
                                <a href="{{ route('admin.servis.audit.show', $log->id) }}"
                                   class="btn btn-sm btn-info shadow-sm"
                                   title="Lihat detail perubahan">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x d-block mb-3 opacity-25"></i>
                                Belum ada aktivitas yang tercatat untuk sistem servis.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Footer card dengan pagination --}}
        <div class="card-footer d-flex justify-content-between align-items-center bg-white border-top-0">
            
            {{-- Info jumlah data --}} 
            <div class="small text-muted">
                Menampilkan 
                <strong>{{ $logs->firstItem() ?? 0 }}</strong>
                –
                <strong>{{ $logs->lastItem() ?? 0 }}</strong>
                dari
                <strong>{{ $logs->total() }}</strong>
                aktivitas
            </div>

            {{-- Navigasi halaman --}}
            @if($logs->hasPages())
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    
                    {{-- Prev --}}
                    <li class="page-item {{ $logs->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" 
                           href="{{ $logs->previousPageUrl() }}"
                           aria-label="Sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    {{-- Nomor halaman --}}
                    @foreach($logs->getUrlRange(
                        max(1, $logs->currentPage() - 2),
                        min($logs->lastPage(), $logs->currentPage() + 2)
                    ) as $page => $url)
                        @if($page == 1 && $logs->currentPage() > 3)
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->url(1) }}">1</a>
                            </li>
                            @if($logs->currentPage() > 4)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            @endif
                        @endif

                        <li class="page-item {{ $page == $logs->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>

                        @if($page == $logs->lastPage() && $logs->currentPage() < $logs->lastPage() - 2)
                            @if($logs->currentPage() < $logs->lastPage() - 3)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->url($logs->lastPage()) }}">
                                    {{ $logs->lastPage() }}
                                </a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    <li class="page-item {{ !$logs->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link"
                           href="{{ $logs->nextPageUrl() }}"
                           aria-label="Selanjutnya">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>

                </ul>
            </nav>
            @endif

        </div>
    </div>
</div>
@endsection
