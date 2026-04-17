@extends('layouts.app')

@section('title', 'Jadwal Lapangan Futsal')

@push('styles')
<style>
    /* ── Tab Nav ── */
    .lapangan-tabs {
        display: flex;
        gap: 0;
        border-bottom: 3px solid #e3e6f0;
        margin-bottom: 0;
        flex-wrap: wrap;
    }
    .lapangan-tab-btn {
        padding: 10px 22px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        background: transparent;
        color: #858796;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -3px;
        transition: color 0.2s, border-color 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .lapangan-tab-btn:hover {
        color: #4e73df;
    }
    .lapangan-tab-btn.active {
        color: #4e73df;
        border-bottom-color: #4e73df;
        background: #f0f4ff;
        border-radius: 8px 8px 0 0;
    }
    .lapangan-tab-btn .tab-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.5;
    }
    .lapangan-tab-btn.active .tab-dot {
        opacity: 1;
    }

    /* ── Table ── */
    .jadwal-panel { display: none; }
    .jadwal-panel.active { display: block; }
    .slot-row-tersedia { background: #f6fffb; }
    .slot-row-terisi   { background: #fff5f5; }

    /* ── Stats mini ── */
    .stat-mini {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        margin-left: 6px;
    }
    .stat-mini.tersedia { background: #d1fae5; color: #047857; }
    .stat-mini.terisi   { background: #fee2e2; color: #dc2626; }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Lapangan</h1>
</div>

{{-- Filter Card --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-1"></i> Filter Jadwal Harian
        </h6>
    </div>
    <div class="card-body">
        <form id="filterForm" class="form-inline mb-0">
            <label for="tanggalFilter" class="mr-2 font-weight-bold text-gray-800">Pilih Tanggal:</label>
            <input type="date" id="tanggalFilter" class="form-control mr-3" value="{{ date('Y-m-d') }}" required>
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-search fa-sm text-white-50 mr-1"></i> Tampilkan
            </button>
        </form>
    </div>
</div>

{{-- Tab Container --}}
<div class="card shadow mb-4">
    <div class="card-header py-0 px-3 pt-3" style="border-bottom: none;">
        {{-- Tab Nav akan diisi oleh JS --}}
        <div id="tabNav" class="lapangan-tabs">
            <div class="text-muted small py-2 px-3">
                <i class="fas fa-spinner fa-spin mr-1"></i> Memuat lapangan...
            </div>
        </div>
    </div>
    <div class="card-body pt-3" id="tabContent">
        {{-- Tab Panels akan diisi oleh JS --}}
        <div class="text-center text-muted py-5">
            <i class="fas fa-spinner fa-spin fa-2x mb-3 text-primary"></i>
            <p>Mengambil data jadwal...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm   = document.getElementById('filterForm');
    const tanggalInput = document.getElementById('tanggalFilter');
    const tabNav       = document.getElementById('tabNav');
    const tabContent   = document.getElementById('tabContent');

    const jamBuka  = "{{ \Carbon\Carbon::parse($pengaturan->jam_buka)->format('H:i') }}";
    const jamTutup = "{{ \Carbon\Carbon::parse($pengaturan->jam_tutup)->format('H:i') }}";

    // Palet warna untuk tab
    const TAB_COLORS = ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc', '#858796'];

    // Load awal
    loadJadwal(tanggalInput.value);

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        loadJadwal(tanggalInput.value);
    });

    function loadJadwal(tanggal) {
        tabNav.innerHTML = `<div class="text-muted small py-2 px-3"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat lapangan...</div>`;
        tabContent.innerHTML = `<div class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin fa-2x mb-3 text-primary"></i><p>Mengambil data jadwal...</p></div>`;

        fetch(`{{ route('adminfutsal.jadwal-lapangan.api') }}?tanggal=${tanggal}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    renderTabs(res.lapangans, res.grouped, tanggal);
                } else {
                    showError('Gagal memuat jadwal dari server.');
                }
            })
            .catch(err => {
                console.error(err);
                showError('Terjadi kesalahan jaringan saat memuat data.');
            });
    }

    function renderTabs(lapangans, grouped, tanggal) {
        if (!lapangans || lapangans.length === 0) {
            tabNav.innerHTML = '';
            tabContent.innerHTML = `<div class="text-center text-muted py-5"><i class="fas fa-exclamation-circle fa-2x mb-3 text-warning"></i><p>Belum ada lapangan yang terdaftar.</p></div>`;
            return;
        }

        let navHtml    = '';
        let panelsHtml = '';

        lapangans.forEach((lapangan, idx) => {
            const slots   = grouped[lapangan.id] || [];
            const color   = TAB_COLORS[idx % TAB_COLORS.length];
            const isFirst = idx === 0;

            const totalTersedia = slots.filter(s => s.status === 'tersedia').length;
            const totalTerisi   = slots.filter(s => s.status !== 'tersedia').length;

            // --- Tab Button ---
            navHtml += `
                <button class="lapangan-tab-btn ${isFirst ? 'active' : ''}"
                        data-tab="panel-${lapangan.id}"
                        style="--tab-color: ${color}; ${isFirst ? 'border-bottom-color:' + color + ';color:' + color : ''}">
                    <i class="fas fa-futbol"></i>
                    ${escHtml(lapangan.nama)}
                    <span class="stat-mini ${totalTerisi > 0 ? 'bg-danger text-white border-none' : 'tersedia'}" 
                          title="${totalTerisi > 0 ? totalTerisi + ' Booking Terisi' : 'Tersedia'}">
                        ${totalTerisi > 0 ? '<i class="fas fa-lock mr-1 small"></i>' + totalTerisi : totalTersedia}
                    </span>
                </button>`;

            // --- Tab Panel ---
            panelsHtml += `
                <div class="jadwal-panel ${isFirst ? 'active' : ''}" id="panel-${lapangan.id}">
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="font-weight-bold mb-1" style="color:${color}">
                                <i class="fas fa-map-marker-alt mr-1"></i> ${escHtml(lapangan.nama)}
                            </h5>
                            <small class="text-muted">
                                ${escHtml(lapangan.deskripsi || lapangan.spesifikasi || 'Lapangan futsal')} &nbsp;|&nbsp; Tanggal: <strong>${formatTanggal(tanggal)}</strong>
                            </small>
                        </div>
                        <div class="text-right d-none d-md-block">
                            <span class="stat-mini tersedia"><i class="fas fa-check-circle mr-1"></i>${totalTersedia} Tersedia</span>
                            <span class="stat-mini terisi ml-1"><i class="fas fa-times-circle mr-1"></i>${totalTerisi} Terisi</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="30%">Jam</th>
                                    <th width="70%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${renderRows(slots, lapangan.id)}
                            </tbody>
                        </table>
                    </div>
                </div>`;
        });

        tabNav.innerHTML    = navHtml;
        tabContent.innerHTML = panelsHtml;

        // Pasang event klik tab
        tabNav.querySelectorAll('.lapangan-tab-btn').forEach((btn, idx) => {
            const color = TAB_COLORS[idx % TAB_COLORS.length];
            btn.addEventListener('click', function () {
                // Reset semua
                tabNav.querySelectorAll('.lapangan-tab-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.borderBottomColor = 'transparent';
                    b.style.color = '';
                    b.style.background = '';
                });
                tabContent.querySelectorAll('.jadwal-panel').forEach(p => p.classList.remove('active'));

                // Aktifkan yang diklik
                this.classList.add('active');
                this.style.borderBottomColor = color;
                this.style.color = color;
                this.style.background = color + '1A'; // ~10% opacity
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });
    }

    function renderRows(slots, lapanganId) {
        let [bukaH] = jamBuka.split(':').map(Number);
        let [tutupH] = jamTutup.split(':').map(Number);
        let html = '';

        for (let h = bukaH; h < tutupH; h++) {
            const strMulai   = pad(h) + ':00';
            const strSelesai = pad(h + 1) + ':00';
            const slot = slots.find(s => s.jam_mulai.substring(0, 5) === strMulai);

            let badge = '';
            let rowClass = '';
            if (!slot) {
                badge = `<span class="badge badge-secondary px-3 py-2"><i class="fas fa-minus mr-1"></i> Belum Digenerate</span>`;
            } else if (slot.status === 'tersedia') {
                rowClass = 'slot-row-tersedia';
                badge = `<span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> Tersedia</span>`;
            } else {
                rowClass = 'slot-row-terisi';
                badge = `<span class="badge badge-danger px-3 py-2"><i class="fas fa-times-circle mr-1"></i> Terisi</span>`;
            }

            html += `<tr class="${rowClass}">
                        <td class="align-middle font-weight-bold text-gray-800">${strMulai} – ${strSelesai}</td>
                        <td class="align-middle text-center">${badge}</td>
                     </tr>`;
        }

        return html || '<tr><td colspan="2" class="text-center text-muted py-4">Tidak ada jam operasional.</td></tr>';
    }

    function showError(msg) {
        tabNav.innerHTML = '';
        tabContent.innerHTML = `<div class="text-center text-danger py-5"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p>${msg}</p></div>`;
    }

    function pad(n) { return ('0' + n).slice(-2); }

    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function formatTanggal(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }
});
</script>
@endpush
