@extends('layouts.publik')
@php $hideNavbarBack = true; @endphp

@section('title', 'Riwayat Sewa')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Sewa</h1>
        <a href="{{ route('user.kantin.sewa.detail', $sewa->access_token) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-sm-1"></i> <span class="d-none d-sm-inline">Kembali</span>
        </a>
    </div>

    <p class="mb-4">Daftar pengajuan sewa untuk nomor handphone: <strong>{{ $sewa->no_hp_snapshot }}</strong></p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penyewaan</h6>
        </div>
        <div class="card-body">
            <!-- Filter & Search Row -->
            <div class="d-md-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <label for="statusFilter" class="small font-weight-bold mr-2 mb-0 d-none d-sm-block">Status:</label>
                    <select id="statusFilter" class="form-control form-control-sm" style="width: auto; min-width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div id="searchContainer" class="d-flex align-items-center">
                    <!-- DataTables Search will be moved here -->
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Unit / Ruko</th>
                            <th>Kode Unit</th>
                            <th>Periode Sewa</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Sewa Aktif / Pending --}}
                        @foreach($sewa_aktif as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->ruko->nama_ruko ?? 'Unit Kantin' }}</strong>
                            </td>
                            <td>{{ $item->ruko->kode_unit }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai_sewa)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai_sewa)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($item->status_sewa === 'pending')
                                    <span class="badge badge-warning">Menunggu Verifikasi</span>
                                @elseif($item->status_sewa === 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('user.kantin.sewa.detail', $item->access_token) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach

                        {{-- Riwayat Lampau --}}
                        @foreach($riwayat as $item)
                        <tr>
                            <td class="text-muted">
                                {{ $item->ruko->nama_ruko ?? 'Unit Kantin' }}
                            </td>
                            <td class="text-muted">{{ $item->ruko->kode_unit }}</td>
                            <td class="text-muted">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai_sewa)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai_sewa)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($item->status_sewa === 'selesai')
                                    <span class="badge badge-primary">Selesai</span>
                                @else
                                    <span class="badge badge-secondary">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('user.kantin.sewa.detail', $item->access_token) }}" class="btn btn-light btn-sm border">
                                    <i class="fas fa-history"></i> Riwayat
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>
    /* Menyesuaikan dengan standar SB Admin 2 */
    .table thead th {
        background-color: #f8f9fc;
        color: #4e73df;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .badge {
        font-weight: 700;
        padding: 0.5em 0.75em;
    }
    /* Memperbaiki tampilan search agar sejajar */
    .dataTables_filter {
        margin-bottom: 0 !important;
    }
    .dataTables_filter label {
        margin-bottom: 0 !important;
        display: flex;
        align-items: center;
    }
    .dataTables_filter input {
        margin-left: 0.5rem !important;
        border-radius: 5px;
        border: 1px solid #d1d3e2;
        padding: 5px 10px;
    }
    /* Responsif Mobile */
    @media (max-width: 576px) {
        .dataTables_filter, .dataTables_filter label {
            width: 100%;
        }
        .dataTables_filter input {
            width: 100% !important;
            margin-left: 0 !important;
            margin-top: 5px;
        }
        #statusFilter {
            width: 100% !important;
        }
        .table td, .table th {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json",
                "search": "Cari:",
            },
            "dom": "<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "order": [],
            "pageLength": 10
        });

        // Pindahkan kotak pencarian bawaan ke container kustom
        // Karena kita mematikan dom default 'f', kita buat search box manual yang terhubung ke API
        var searchInput = $('<input type="search" class="form-control form-control-sm" placeholder="Cari data...">')
            .appendTo('#searchContainer')
            .on('keyup', function() {
                table.search(this.value).draw();
            });
        
        $('<label class="small font-weight-bold mr-2 mb-0 d-none d-sm-block">Cari:</label>')
            .prependTo('#searchContainer');

        // Event listener untuk filter status
        $('#statusFilter').on('change', function() {
            table.column(3).search(this.value).draw();
        });
    });
</script>
@endpush
