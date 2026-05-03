@extends('layouts.publik')

@section('title', 'Layanan Servis AC')

@push('styles')
<style>
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(0, 0, 0, 0.05);
    }
    .card-header {
        background-color: #f8f9fc !important;
        border-bottom: 1px solid #e3e6f0 !important;
        font-weight: bold;
    }
    .badge-pending { background-color: #f6c23e; color: white; }
    .badge-proses { background-color: #36b9cc; color: white; }
    .badge-selesai { background-color: #1cc88a; color: white; }
    .badge-canceled { background-color: #e74a3b; color: white; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if(!auth()->check())
    <!-- Landing for Guest -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
            <div class="py-5">
                <h1 class="display-4 font-weight-bold text-primary mb-3">Servis AC Jadi Lebih Mudah</h1>
                <p class="lead text-gray-700 mb-4">Tanpa perlu login, pesan layanan servis AC Anda sekarang dan pantau statusnya langsung dari WhatsApp.</p>
                <div class="d-flex justify-content-center" style="gap: 15px;">
                    <button class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill" onclick="openLayananModal()">
                        <i class="fas fa-plus-circle mr-2"></i> Booking Sekarang
                    </button>
                    <a href="{{ route('user.gateway') }}" class="btn btn-outline-secondary btn-lg px-5 rounded-pill">
                        <i class="fas fa-home mr-2"></i> Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Page Heading for Logged In User -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
        <h1 class="h3 mb-3 mb-sm-0 text-gray-800 font-weight-bold">Daftar Booking & Riwayat Servis AC</h1>
        <button class="btn btn-sm btn-primary" onclick="openLayananModal()">
            <i class="fas fa-plus mr-1"></i> Tambah Booking
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Booking</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">On Process</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['proses'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selesai Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['selesai'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Riwayat Booking Anda</h6>
            <form action="{{ route('user.ac.index') }}" method="GET" id="filterForm" class="form-inline w-100 w-md-auto">
                <select name="status" class="form-control form-control-sm w-100" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ $statusFilter == 'menunggu' ? 'selected' : '' }}>Pending</option>
                    <option value="proses" {{ $statusFilter == 'proses' ? 'selected' : '' }}>On Process</option>
                    <option value="selesai" {{ $statusFilter == 'selesai' ? 'selected' : '' }}>Completed</option>
                    <option value="canceled" {{ $statusFilter == 'canceled' ? 'selected' : '' }}>Canceled</option>
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Kunjungan</th>
                            <th>Layanan</th>
                            <th>Status (Booking)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        @php
                            $isCanceled = $booking->booking->status === 'dibatalkan';
                            $statusLabel = $booking->status;
                            $badgeClass = 'pending';

                            if($isCanceled) {
                                $statusLabel = 'Canceled';
                                $badgeClass = 'canceled';
                            } elseif($booking->status == 'proses') {
                                $statusLabel = 'On Process';
                                $badgeClass = 'proses';
                            } elseif($booking->status == 'selesai') {
                                $statusLabel = 'Completed';
                                $badgeClass = 'selesai';
                            }
                        @endphp
                        <tr>
                            <td>{{ ($bookings->currentPage()-1) * $bookings->perPage() + $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->tgl_kunjungan)->format('d/m/Y') }}</td>
                            <td>{{ $booking->layanan->nama ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $badgeClass }} px-3 py-2">
                                    {{ ucfirst($statusLabel) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('user.ac.token.show', $booking->access_token) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted italic">
                                Belum ada riwayat booking.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Booking -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Formulir Booking Servis AC</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('user.ac.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div id="bookingErrors" class="alert alert-danger d-none"></div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold text-dark text-uppercase">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control form-control-lg bg-light border-0 shadow-sm" value="{{ auth()->user()->nama_lengkap ?? '' }}" required placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold text-dark text-uppercase">Nomor WhatsApp (Aktif)</label>
                            <input type="text" name="no_hp" class="form-control form-control-lg bg-light border-0 shadow-sm" value="{{ auth()->user()->no_hp ?? '' }}" required placeholder="Contoh: 08123456789">
                            <small class="text-muted">Link status booking akan dikirim ke nomor ini.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold text-dark text-uppercase">Pilih Layanan</label>
                            <select name="layanan_id" id="layanan_id" class="form-control form-control-lg bg-light border-0 shadow-sm" required>
                                <option value="">-- Pilih Layanan --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold text-dark text-uppercase">Tanggal Kunjungan</label>
                            <input type="date" name="tgl_kunjungan" class="form-control form-control-lg bg-light border-0 shadow-sm" required min="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control bg-light border-0 shadow-sm" rows="2" required placeholder="Jl. Merpati No. 123, Kel. Sukamaju, Kec. Cilodong..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold text-dark text-uppercase">Merek AC (Opsional)</label>
                            <input type="text" name="merek_ac" class="form-control bg-light border-0 shadow-sm" placeholder="Contoh: Sharp, LG, Samsung">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold text-dark text-uppercase">Detail Keluhan (Opsional)</label>
                            <textarea name="detail_keluhan" class="form-control bg-light border-0 shadow-sm" rows="1" placeholder="Contoh: AC tidak dingin / Berisik"></textarea>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small mb-0 mt-2">
                        <i class="fas fa-info-circle mr-2"></i> Klik tombol di bawah untuk membuat pesanan. Teknisi akan segera menghubungi Anda.
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-5 shadow-sm font-weight-bold">Konfirmasi Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="detailModalLabel">Detail Booking Layanan AC</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancel -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan booking ini?</p>
                    <div class="form-group">
                        <label for="alasan" class="font-weight-bold text-dark">Alasan Pembatalan</label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary border-0" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger border-0">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    function openLayananModal() {
        // Load semua layanan ke select option
        $.ajax({
            url: '{{ route("user.ac.layanan") }}',
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(data) {
                let optionHtml = '<option value="">-- Pilih Layanan --</option>';
                if (data.layanans && data.layanans.data) {
                    data.layanans.data.forEach(function(layanan) {
                        optionHtml += `<option value="${layanan.id}">${layanan.nama} (Rp ${new Intl.NumberFormat('id-ID').format(layanan.harga_jasa)})</option>`;
                    });
                }
                $('#layanan_id').html(optionHtml);
                $('#bookingModal').modal('show');
            },
            error: function() {
                alert('Gagal memuat layanan');
            }
        });
    }

    function selectLayanan(layananId, layananNama) {
        $('#layananModal').modal('hide');
        $('#layanan_id').val(layananId);
        $('#layananName').val(layananNama);
        $('#bookingErrors').addClass('d-none');
        $('#bookingModal').modal('show');
    }

    function viewDetail(id) {
        $('#detailContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#detailModal').modal('show');

        $.get(`/user/ac/${id}`, function(data) {
            console.log('Booking detail data:', data);

            let detailHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Informasi Layanan</h6>
                        <table class="table table-sm table-borderless text-dark">
                            <tr><td width="40%">Layanan</td><td>: <b>${data.layanan?.nama || '-'}</b></td></tr>
                            <tr><td>Merek AC</td><td>: ${data.merek_ac || '-'}</td></tr>
                            <tr><td>Tgl Kunjungan</td><td>: ${moment(data.tgl_kunjungan).format('DD/MM/YYYY')}</td></tr>
                            <tr><td>Alamat</td><td>: ${data.alamat || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-tools mr-2"></i>Status & Pembayaran</h6>
                        <table class="table table-sm table-borderless text-dark">
                            <tr><td width="40%">Status Booking</td><td>: <b>${data.status?.charAt(0).toUpperCase() + (data.status?.slice(1) || '')}</b></td></tr>
                            <tr><td>Status Bayar</td><td>: ${data.pembayaran ? '<b>' + (data.pembayaran.status?.charAt(0).toUpperCase() + data.pembayaran.status?.slice(1) || 'N/A') + '</b>' : '<span class="text-muted">Belum ada invoice</span>'}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-file-alt mr-2"></i>Detail Keluhan</h6>
                        <div class="alert alert-light border p-3">
                            <p class="mb-0 text-dark">${data.detail_keluhan ? data.detail_keluhan : '<em class="text-muted">Tidak ada detail keluhan</em>'}</p>
                        </div>
                    </div>
                </div>
            `;
            $('#detailContent').html(detailHtml);
        }).fail(function(xhr, status, error) {
            console.error('Error loading detail:', error, xhr);
            let errorMsg = 'Gagal memuat detail booking';
            if (xhr.status === 404) {
                errorMsg = 'Booking tidak ditemukan';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            $('#detailContent').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i>${errorMsg}</div>`);
        });
    }

    function openCancelModal(id) {
        $('#cancelForm').attr('action', `/user/ac/${id}/cancel`);
        $('#cancelModal').modal('show');
    }


</script>
@endpush
