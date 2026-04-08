@extends('layouts.app')

@section('title', 'Futsal Dashboard')

@section('content')
<div class="container-fluid" x-data="futsalDashboard()">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Futsal Dashboard</h1>
        <a href="{{ route('adminfutsal.jadwal-lapangan.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Booking Lapangan Baru
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total Booking Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Booking</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Aktif Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Booking Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['aktif'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histori Selesai Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Histori Selesai
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $summary['history'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Recent Bookings -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                    <a href="{{ route('user.futsal.history') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                        <i class="fas fa-list fa-sm"></i> Lihat Histori
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th>Lapangan</th>
                                    <th>Tanggal Main</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent as $item)
                                <tr class="text-center">
                                    <td class="font-weight-bold">{{ $item->lapangan->nama ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_main)->format('d M Y') }}</td>
                                    <td>{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</td>
                                    <td>
                                        @php
                                            $status = strtolower($item->booking->status ?? '');
                                            $badge = match($status) {
                                                'menunggu' => 'badge-warning text-dark',
                                                'dikonfirmasi' => 'badge-primary',
                                                'selesai' => 'badge-success',
                                                'dibatalkan' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge font-weight-bold {{ $badge }}">{{ ucfirst($status ?: 'Menunggu') }}</span>
                                    </td>
                                    <td>
                                        <button @click="openDetail({{ $item->id }})" class="btn btn-info btn-sm btn-circle shadow-sm" title="Detail">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">Belum ada booking.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Component -->
    @include('user.futsal.partials.modal-detail')

</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@endpush

@push('scripts')
<script>
function futsalDashboard() {
    return {
        modalLoading: false,
        downloading: false,
        detail: {
            id: null,
            lapangan: { nama: '', ukuran: '', deskripsi: '' },
            jadwal: { tanggal: '', jam: '', durasi: '' },
            status: { label: '', color: '' },
            pembayaran: { jenis: '', total: '', status: '', metode: '' }
        },
        async openDetail(id) {
            this.modalLoading = true;
            $('#detailModal').modal('show');
            try {
                const response = await axios.get("{{ url('user/futsal/api/booking') }}/" + id);
                this.detail = response.data;
                this.detail.id = id;
            } catch (error) {
                console.error("Detail error:", error);
                $('#detailModal').modal('hide');
            } finally {
                this.modalLoading = false;
            }
        },
        async downloadPDF(id) {
            if (this.downloading) return;
            this.downloading = true;
            try {
                const response = await axios.get("{{ url('user/futsal/booking') }}/" + id + "/download-invoice", {
                    responseType: 'blob'
                });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Invoice-Futsal-' + id + '.pdf');
                document.body.appendChild(link);
                link.click();
                link.remove();
            } catch (error) {
                console.error("Download error:", error);
                alert("Gagal mengunduh PDF. Silakan coba lagi.");
            } finally {
                this.downloading = false;
            }
        }
    }
}
</script>
@endpush
