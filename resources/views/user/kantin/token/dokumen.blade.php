@extends('layouts.publik')
@php $hideNavbarBack = true; @endphp

@section('title', 'Dokumen Penyewaan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dokumen Penyewaan</h1>
        <a href="{{ route('user.kantin.sewa.detail', $sewa->access_token) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-sm-1"></i> <span class="d-none d-sm-inline">Kembali</span>
        </a>
    </div>

    <p class="mb-4">Daftar dokumen resmi, MOU, dan berkas terkait unit: <strong>{{ $sewa->ruko->kode_unit }}</strong></p>

    <!-- Documents Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Berkas & Dokumen</h6>
        </div>
        <div class="card-body">
            <!-- Filter & Search Row -->
            <div class="d-md-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <label for="uploaderFilter" class="small font-weight-bold mr-2 mb-0 d-none d-sm-block">Oleh:</label>
                    <select id="uploaderFilter" class="form-control form-control-sm" style="width: auto; min-width: 150px;">
                        <option value="">Semua</option>
                        <option value="Sistem">Sistem</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div id="searchContainer" class="d-flex align-items-center">
                    <!-- Search box will be moved here -->
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">Tipe</th>
                            <th>Nama Dokumen</th>
                            <th>Tanggal Terbit</th>
                            <th>Oleh</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sewa->dokumen as $dok)
                        <tr>
                            <td class="text-center">
                                @if(Str::endsWith($dok->path_file, '.pdf'))
                                    <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                @else
                                    <i class="fas fa-file-image text-info fa-lg"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $dok->path_file) }}" target="_blank" class="text-primary font-weight-bold">
                                    {{ $dok->nama_dokumen }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($dok->created_at)->format('d/m/Y') }}</td>
                            <td><span class="badge badge-light">{{ ucfirst($dok->diunggah_oleh ?? 'Sistem') }}</span></td>
                            <td class="text-center">
                                <a href="{{ asset('storage/' . $dok->path_file) }}" download="{{ $dok->nama_dokumen }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Instructions Box -->
    <div class="card bg-light shadow-sm border-left-primary mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Petunjuk Dokumen</h6>
            <ul class="small text-muted mb-0">
                <li>Dokumen MOU (Memorandum of Understanding) dapat diunduh untuk keperluan administrasi mandiri.</li>
                <li>Gunakan aplikasi Adobe Reader atau sejenisnya untuk membuka file PDF.</li>
                <li>Jika terdapat perbedaan data pada dokumen, harap segera hubungi admin BLUD.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>
    .table thead th {
        background-color: #f8f9fc;
        color: #4e73df;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .dataTables_filter input {
        border-radius: 5px;
        border: 1px solid #d1d3e2;
        padding: 5px 10px;
    }
    /* Responsif Mobile */
    @media (max-width: 576px) {
        #searchContainer, #searchContainer input {
            width: 100% !important;
        }
        #uploaderFilter {
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
                "url": "//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json"
            },
            "dom": "<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "order": [],
            "pageLength": 10
        });

        // Pindahkan kotak pencarian bawaan ke container kustom
        var searchInput = $('<input type="search" class="form-control form-control-sm" placeholder="Cari dokumen...">')
            .appendTo('#searchContainer')
            .on('keyup', function() {
                table.search(this.value).draw();
            });
        
        $('<label class="small font-weight-bold mr-2 mb-0 d-none d-sm-block">Cari:</label>')
            .prependTo('#searchContainer');

        // Event listener untuk filter pengunggah
        $('#uploaderFilter').on('change', function() {
            table.column(3).search(this.value).draw();
        });
    });
</script>
@endpush
