@extends('layouts.publik')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-11">
            {{-- Header Card --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-4 text-white d-flex align-items-center justify-content-between" 
                     style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mr-4" 
                             style="width: 60px; height: 60px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            <i class="fas fa-history fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-weight-bold mb-0">Riwayat Booking Anda</h3>
                            <p class="mb-0 opacity-75">Nomor Terdaftar: <span class="badge badge-light text-primary">+{{ $no_hp }}</span></p>
                        </div>
                    </div>
                    <div class="d-none d-md-block">
                        <a href="{{ route('user.gateway') }}" class="btn btn-light rounded-pill px-4 font-weight-bold">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            {{-- Main Table Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <div class="row align-items-center">
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <h5 class="font-weight-bold text-gray-800 mb-0">
                                <i class="fas fa-list mr-2 text-primary"></i>Daftar Booking
                            </h5>
                        </div>
                        <div class="col-lg-8">
                            <div class="row no-gutters justify-content-end">
                                {{-- Filter Kategori --}}
                                <div class="col-md-4 px-1 mb-2 mb-md-0">
                                    <select id="filterCategory" class="form-control rounded-pill border-gray-200" style="font-size: 0.9rem;">
                                        <option value="">Semua Kategori</option>
                                        <option value="Futsal">Layanan Futsal</option>
                                        <option value="Servis AC">Servis AC</option>
                                        <option value="Servis Kendaraan">Servis Kendaraan</option>
                                        <option value="Kantin">Sewa Kantin</option>
                                    </select>
                                </div>
                                {{-- Filter Status --}}
                                <div class="col-md-4 px-1">
                                    <select id="filterStatus" class="form-control rounded-pill border-gray-200" style="font-size: 0.9rem;">
                                        <option value="">Semua Status</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="pending">Menunggu</option>
                                        <option value="proses">Proses</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="dibatalkan">Dibatalkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table table-hover" id="bookingTable" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-gray-400 small text-uppercase font-weight-bold">
                                    <th>ID Booking</th>
                                    <th>Layanan</th>
                                    <th>Unit / Nama</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="font-weight-bold text-primary">{{ $booking->kode_booking }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-light border px-2 py-1 text-muted" style="font-weight: 500;">{{ $booking->category }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-gray-800">{{ $booking->unit_name }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small font-weight-bold text-gray-700">
                                                <i class="far fa-calendar-alt mr-1 text-primary"></i>
                                                {{ $booking->date ? \Carbon\Carbon::parse($booking->date)->format('d M Y') : 'N/A' }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="far fa-clock mr-1"></i> {{ $booking->time }}
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @php
                                                $status = strtolower($booking->status);
                                                $badgeClass = 'badge-secondary';
                                                if(in_array($status, ['aktif', 'dikonfirmasi', 'selesai'])) $badgeClass = 'badge-success';
                                                if(in_array($status, ['pending', 'menunggu', 'bayar', 'menunggu konfirmasi'])) $badgeClass = 'badge-warning';
                                                if(in_array($status, ['proses'])) $badgeClass = 'badge-info';
                                                if(in_array($status, ['dibatalkan', 'ditolak'])) $badgeClass = 'badge-danger';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }} px-3 py-2 text-uppercase" style="font-size: 0.7rem; border-radius: 6px; min-width: 80px; text-align: center;">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="{{ $booking->detail_url }}" 
                                               class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm font-weight-bold">
                                                <i class="fas fa-search-plus mr-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="text-center mt-2">
                <div class="alert alert-white border shadow-sm d-inline-block px-4 py-3" style="border-radius: 15px; background: white;">
                    <span class="text-muted small">
                        <i class="fas fa-shield-alt mr-2 text-primary"></i>
                        Sesi akses riwayat Anda bersifat sementara dan akan berakhir dalam <strong>15 menit</strong>.
                    </span>
                    <hr class="my-2">
                    <a href="{{ route('user.gateway') }}" class="text-danger small font-weight-bold text-decoration-none">
                        <i class="fas fa-sign-out-alt mr-1"></i> Akhiri Sesi Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fc; }
    .table thead th { border-top: none; border-bottom: 2px solid #eef2f7 !important; color: #b7c1d1; letter-spacing: 0.5px; }
    .table tbody td { border-bottom: 1px solid #f0f2f5; padding: 1.25rem 0.75rem !important; }
    .table tr:last-child td { border-bottom: none; }
    
    /* DataTable Custom Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin-left: 2px;
        border-radius: 8px !important;
        border: none !important;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4e73df !important;
        color: white !important;
    }
    .dataTables_filter input {
        border-radius: 20px !important;
        border: 1px solid #e3e6f0 !important;
        padding: 0.5rem 1rem !important;
        margin-left: 10px !important;
        font-size: 0.9rem;
    }
    .dataTables_length select {
        border-radius: 10px !important;
        border: 1px solid #e3e6f0 !important;
        padding: 0.25rem 0.5rem !important;
    }
</style>

@push('scripts')
<link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        const table = $('#bookingTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
                "search": "",
                "searchPlaceholder": "Cari data booking...",
                "lengthMenu": "Tampilkan _MENU_",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "next": "Next",
                    "previous": "Prev"
                }
            },
            "pageLength": 10,
            "order": [[3, "desc"]], // Urutkan berdasarkan jadwal terbaru
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Matikan sorting untuk kolom Aksi
            ],
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Filter Kategori
        $('#filterCategory').on('change', function() {
            table.column(1).search(this.value).draw();
        });

        // Filter Status
        $('#filterStatus').on('change', function() {
            table.column(4).search(this.value).draw();
        });
    });
</script>
@endpush
@endsection
