@extends('layouts.app')

@section('title', 'Histori Booking Layanan AC')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('user.ac.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Histori Booking</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Histori Booking Layanan AC</h1>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('user.ac.history') }}" class="btn btn-sm btn-light border shadow-sm mr-2 text-primary">
                <i class="fas fa-sync-alt"></i> Refresh
            </a>
        </div>
    </div>

    <!-- DataTales Card -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Booking Selesai</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                    <thead class="bg-light text-gray-800">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Booking</th>
                            <th>Layanan</th>
                            <th>Teknisi</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                        <tr>
                            <td>{{ ($history->currentPage()-1) * $history->perPage() + $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                            <td class="text-left">
                                <span class="font-weight-bold text-primary">{{ $item->layanan->nama ?? 'N/A' }}</span><br>
                                <small class="text-muted">{{ $item->merek_ac }}</small>
                            </td>
                            <td>{{ $item->teknisi->name ?? 'Belum Ditentukan' }}</td>
                            <td class="font-weight-bold">
                                Rp {{ number_format($item->pembayaran->total_harga ?? 0, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge badge-success px-3 py-2">Selesai</span>
                            </td>
                            <td>
                                <button onclick="viewDetail({{ $item->id }})" class="btn btn-info btn-sm shadow-sm rounded-pill px-3">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5">
                                <i class="fas fa-history fa-3x text-gray-200 mb-3"></i>
                                <h5 class="text-gray-400 font-weight-bold">Belum ada riwayat booking</h5>
                                <p class="text-muted small">Booking yang sudah selesai akan muncul di sini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Links -->
            <div class="d-flex justify-content-end mt-4">
                {{ $history->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="detailModalLabel">Rincian Riwayat Servis AC</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <!-- Data akan dimuat via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .breadcrumb-item + .breadcrumb-item::before {
        content: "\f105";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 10px;
        padding-right: 8px;
        color: #d1d3e2;
    }
    .card { border-radius: 12px; }
    .table thead th { border-top: none; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
function viewDetail(id) {
    $('#detailContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
    $('#detailModal').modal('show');

    $.get(`/user/ac/${id}`, function(data) {
        let itemsHtml = '';
        if(data.rincian_servis && data.rincian_servis.length > 0) {
            data.rincian_servis.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td class="text-left">${item.item}</td>
                        <td>${item.quantity} ${item.satuan}</td>
                        <td class="text-right">Rp ${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
                        <td class="text-right font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                    </tr>
                `;
            });
        } else {
            itemsHtml = '<tr><td colspan="4" class="text-center text-muted">Tidak ada rincian item</td></tr>';
        }

        let detailHtml = `
            <div class="row mb-4">
                <div class="col-md-6 border-right">
                    <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Informasi Booking</h6>
                    <table class="table table-sm table-borderless text-dark">
                        <tr><td width="40%">No. Booking</td><td>: <b>#${data.booking.id}</b></td></tr>
                        <tr><td>Tgl. Kunjungan</td><td>: ${moment(data.tgl_kunjungan).format('DD MMMM YYYY')}</td></tr>
                        <tr><td>Merek AC</td><td>: ${data.merek_ac}</td></tr>
                        <tr><td>Status</td><td>: <span class="badge badge-success px-3 font-weight-bold">${data.status.toUpperCase()}</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-user-tie mr-2"></i>Informasi Teknisi</h6>
                    <table class="table table-sm table-borderless text-dark">
                        <tr><td width="40%">Nama Teknisi</td><td>: <b>${data.teknisi ? data.teknisi.name : '-'}</b></td></tr>
                        <tr><td>Kontak</td><td>: ${data.teknisi ? (data.teknisi.no_hp || '-') : '-'}</td></tr>
                    </table>
                </div>
            </div>

            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-list mr-2"></i>Rincian Layanan & Biaya</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th>Item/Pekerjaan</th>
                            <th>Qty/Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="3" class="text-right">Total Biaya</th>
                            <th class="text-right text-primary h5 font-weight-bold">
                                Rp ${new Intl.NumberFormat('id-ID').format(data.pembayaran ? data.pembayaran.total_harga : 0)}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 p-3 bg-light rounded shadow-sm border-left-primary">
                <span class="font-weight-bold d-block mb-1 text-primary"><i class="fas fa-comment shadow-sm-alt mr-1"></i> Keluhan Pelanggan:</span>
                <p class="mb-0 text-dark small font-italic">"${data.detail_keluhan || '-'}"</p>
            </div>
        `;
        $('#detailContent').html(detailHtml);
    }).fail(function() {
        $('#detailContent').html('<div class="alert alert-danger">Gagal memuat detail data.</div>');
    });
}
</script>
@endpush
