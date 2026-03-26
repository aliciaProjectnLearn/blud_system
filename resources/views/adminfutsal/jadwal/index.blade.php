@extends('layouts.app')

@section('title', 'Jadwal Lapangan Futsal')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Lapangan</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Filter Jadwal Harian</h6>
    </div>
    <div class="card-body">
        <form id="filterForm" class="form-inline mb-4">
            <label for="tanggalFilter" class="mr-2 font-weight-bold text-gray-800">Pilih Tanggal:</label>
            <input type="date" id="tanggalFilter" class="form-control mr-3" value="{{ date('Y-m-d') }}" required>
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-search fa-sm text-white-50 mr-1"></i> Tampilkan
            </button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="30%">Jam</th>
                        <th width="40%">Nama Lapangan</th>
                        <th width="30%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="jadwalTableBody">
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Memuat data jadwal...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const tanggalInput = document.getElementById('tanggalFilter');
    const tbody = document.getElementById('jadwalTableBody');

    // Load initial data
    loadJadwal(tanggalInput.value);

    // Handle form submit
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        loadJadwal(tanggalInput.value);
    });

    function loadJadwal(tanggal) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil data...</td></tr>';

        // Endpoint: /adminfutsal/jadwal-lapangan/api-by-tanggal
        fetch(`{{ route('adminfutsal.jadwal-lapangan.api') }}?tanggal=${tanggal}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    renderTable(res.data);
                } else {
                    showError('Gagal memuat jadwal dari server.');
                }
            })
            .catch(err => {
                console.error('Error fetching jadwal:', err);
                showError('Terjadi kesalahan jaringan/server saat memuat data jadawal.');
            });
    }

    function renderTable(data) {
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada jadwal lapangan yang ditemukan untuk tanggal ini.</td></tr>';
            return;
        }

        let html = '';
        data.forEach(item => {
            // Cut seconds formatting ('08:00:00' -> '08:00')
            const jamMulai = item.jam_mulai.substring(0, 5);
            const jamSelesai = item.jam_selesai.substring(0, 5);
            
            // Nama Lapangan by relation
            const namaLapangan = item.lapangan ? item.lapangan.nama : 'Lapangan Tidak Diketahui';
            
            // Generate visual badges based on status
            let badgeHtml = '';
            let rowClass = ''; // optional coloring
            if (item.status === 'tersedia') {
                badgeHtml = `<span class="badge badge-success px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-check-circle mr-1"></i> Tersedia</span>`;
            } else {
                badgeHtml = `<span class="badge badge-danger px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-times-circle mr-1"></i> Terisi</span>`;
            }

            html += `
                <tr class="${rowClass}">
                    <td class="align-middle font-weight-bold text-gray-800">${jamMulai} - ${jamSelesai}</td>
                    <td class="align-middle">${namaLapangan}</td>
                    <td class="align-middle text-center">${badgeHtml}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function showError(message) {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger py-4 font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> ${message}</td></tr>`;
    }
});
</script>
@endpush
