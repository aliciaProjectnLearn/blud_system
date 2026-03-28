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
    
    // Data Pengaturan
    const jamBuka = "{{ \Carbon\Carbon::parse($pengaturan->jam_buka)->format('H:i') }}";
    const jamTutup = "{{ \Carbon\Carbon::parse($pengaturan->jam_tutup)->format('H:i') }}";

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
        let html = '';
        
        // Parse jamBuka and jamTutup
        let [bukaHour, bukaMin] = jamBuka.split(':').map(Number);
        let [tutupHour, tutupMin] = jamTutup.split(':').map(Number);
        
        let currentHour = bukaHour;
        
        while (currentHour < tutupHour) {
            let nextHour = currentHour + 1;
            let strJamMulai = ('0' + currentHour).slice(-2) + ':00';
            let strJamSelesai = ('0' + nextHour).slice(-2) + ':00';
            
            // Check if there's any data for this slot from API
            // Usually data has item.jam_mulai like "08:00:00"
            let slotData = data ? data.find(item => item.jam_mulai.substring(0, 5) === strJamMulai) : null;
            
            let namaLapangan = slotData && slotData.lapangan ? slotData.lapangan.nama : '-';
            
            let badgeHtml = '';
            if (!slotData) {
                badgeHtml = `<span class="badge badge-secondary px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-minus mr-1"></i> Kosong (Belum Digenerate)</span>`;
            } else if (slotData.status === 'tersedia') {
                badgeHtml = `<span class="badge badge-success px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-check-circle mr-1"></i> Tersedia</span>`;
            } else {
                badgeHtml = `<span class="badge badge-danger px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-times-circle mr-1"></i> Terisi</span>`;
            }

            html += `
                <tr>
                    <td class="align-middle font-weight-bold text-gray-800">${strJamMulai} - ${strJamSelesai}</td>
                    <td class="align-middle">${namaLapangan}</td>
                    <td class="align-middle text-center">${badgeHtml}</td>
                </tr>
            `;
            
            currentHour++;
        }

        if (html === '') {
            html = '<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada jam operasional.</td></tr>';
        }
        
        tbody.innerHTML = html;
    }

    function showError(message) {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger py-4 font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> ${message}</td></tr>`;
    }
});
</script>
@endpush
