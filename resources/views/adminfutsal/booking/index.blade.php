@extends('layouts.app')

@section('title', 'Manajemen Booking')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Booking Futsal</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addBookingModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Booking
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Booking Futsal</h6>
            
            <!-- Filter Form -->
            <form method="GET" action="{{ route('adminfutsal.booking.index') }}" class="form-inline">
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari pemesan/email..." value="{{ request('search') }}">
                <input type="date" name="tanggal" class="form-control form-control-sm mr-2" value="{{ request('tanggal') }}">
                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                @if(request()->filled('tanggal') || request()->filled('status') || request()->filled('search'))
                    <a href="{{ route('adminfutsal.booking.index') }}" class="btn btn-sm btn-light ml-1">Reset</a>
                @endif
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Data</th>
                            <th>Jadwal Main</th>
                            <th>Durasi</th>
                            <th>Pemesan</th>
                            <th>Lapangan</th>
                            <th>Jenis Pesanan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $index => $item)
                        <tr>
                            <td>{{ $bookings->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_main)->format('d M Y') }}<br><strong>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</strong></td>
                            <td>{{ $item->durasi_main }} Jam</td>
                            <td>{{ $item->user->name ?? 'User Tidak Diketahui' }}</td>
                            <td>{{ $item->lapangan->nama ?? 'Lapangan X' }}</td>
                            <td>
                                @if($item->jenis_pembayaran == 'membership')
                                    <span class="badge badge-info"><i class="fas fa-id-card"></i> Membership</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-money-bill"></i> Reguler</span>
                                @endif
                            </td>
                            <td>
                                @if($item->booking && $item->booking->status == 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($item->booking && $item->booking->status == 'dikonfirmasi')
                                    <span class="badge badge-primary">Dikonfirmasi</span>
                                @elseif($item->booking && $item->booking->status == 'dibatalkan')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-warning text-dark">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                <!-- Info / Detail Button -->
                                <button type="button" class="btn btn-info btn-circle btn-sm" title="Detail Booking" data-toggle="modal" data-target="#detailModal{{ $item->id }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                
                                @if($item->booking && $item->booking->status != 'dibatalkan')
                                    @if($item->booking->status != 'selesai')
                                        <!-- Edit Reschedule -->
                                        <button type="button" class="btn btn-primary btn-circle btn-sm" title="Edit/Reschedule Booking" data-toggle="modal" data-target="#editModal{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <!-- Tandai Selesai -->
                                        <button type="button" class="btn btn-success btn-circle btn-sm" title="Tandai Selesai" data-toggle="modal" data-target="#selesaiModal{{ $item->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                    <!-- Cancel Button -->
                                    <button type="button" class="btn btn-danger btn-circle btn-sm" title="Batalkan Booking" data-toggle="modal" data-target="#cancelModal{{ $item->id }}">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Booking</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <p><strong>Nama Pemesan:</strong> {{ $item->user->name ?? '-' }}</p>
                                        <p><strong>Lapangan:</strong> {{ $item->lapangan->nama ?? '-' }}</p>
                                        <p><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($item->tgl_main)->format('d F Y') }}, {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</p>
                                        <p><strong>Durasi:</strong> {{ $item->durasi_main }} Jam</p>
                                        <p><strong>Jenis Pembayaran:</strong> {{ ucfirst($item->jenis_pembayaran) }}</p>
                                        <p><strong>Status:</strong> {{ ucfirst($item->booking->status ?? '-') }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Reschedule Modal -->
                        @if($item->booking && $item->booking->status != 'dibatalkan' && $item->booking->status != 'selesai')
                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('adminfutsal.booking.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-content text-left">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Edit / Reschedule Jadwal</h5>
                                            <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Lapangan</label>
                                                <select name="lapangan_id" class="form-control" required>
                                                    @foreach($lapangans as $lap)
                                                        <option value="{{ $lap->id }}" {{ $item->lapangan_id == $lap->id ? 'selected' : '' }}>{{ $lap->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Main</label>
                                                <input type="date" name="tgl_main" class="form-control" value="{{ \Carbon\Carbon::parse($item->tgl_main)->format('Y-m-d') }}" required min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Jam Mulai</label>
                                                <select name="jam_mulai" class="form-control jam-mulai-edit" data-id="{{ $item->id }}" required>
                                                    <option value="">-- Pilih Jam Mulai --</option>
                                                    @foreach($jadwalLapangan as $jadwal)
                                                        <option value="{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}" {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i:s') == $jadwal->jam_mulai || \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') == \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') ? 'selected' : '' }}>
                                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Jam Selesai</label>
                                                <input type="time" name="jam_selesai" id="jam_selesai_edit_{{ $item->id }}" class="form-control" value="{{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}" readonly required>
                                                <small class="text-muted">Otomatis 1 jam (berdasarkan jadwal)</small>
                                            </div>
                                            
                                            @if($item->jenis_pembayaran == 'membership')
                                                <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Perubahan durasi akan mempengaruhi otomatis sisa kuota membership.</small>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tandai Selesai Modal -->
                        <div class="modal fade" id="selesaiModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content text-left">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Tandai Selesai</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Menandai booking selesai akan memperbarui status reservasi menjadi Selesai. Lanjutkan?
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                        <form action="{{ route('adminfutsal.booking.selesai', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">Ya, Tandai Selesai</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Cancel Modal -->
                        @if($item->booking && $item->booking->status != 'dibatalkan')
                        <div class="modal fade" id="cancelModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content text-left">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Pembatalan</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin membatalkan jadwal ini?<br>
                                        Pemain: <strong>{{ $item->user->name ?? '-' }}</strong> pada <strong>{{ \Carbon\Carbon::parse($item->tgl_main)->format('d M') }} ({{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }})</strong>.
                                        <br><br>
                                        @if($item->jenis_pembayaran == 'membership')
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Membatalkan booking membership akan mengembalikan kuota member tersebut.</span>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                                        <form action="{{ route('adminfutsal.booking.cancel', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger">Batalkan Jadwal</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data booking.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $bookings->withQueryString()->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Create Booking Modal -->
<div class="modal fade" id="addBookingModal" tabindex="-1" role="dialog" aria-labelledby="addBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('adminfutsal.booking.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addBookingModalLabel">Tambah Data Booking</h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id">Pilih Pelanggan / User <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lapangan_id">Pilih Lapangan <span class="text-danger">*</span></label>
                                <select name="lapangan_id" id="lapangan_id" class="form-control" required>
                                    <option value="">-- Pilih Lapangan --</option>
                                    @foreach($lapangans as $lap)
                                        <option value="{{ $lap->id }}">{{ $lap->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tgl_main">Tanggal Main <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_main" id="tgl_main" class="form-control" required min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
                                <select name="jam_mulai" id="jam_mulai" class="form-control" required>
                                    <option value="">-- Pilih Jam --</option>
                                    @foreach($jadwalLapangan as $jadwal)
                                        <option value="{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}">
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required readonly>
                                <small class="text-muted">Otomatis 1 jam</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label>Jenis Pembayaran <span class="text-danger">*</span></label>
                                <div class="mt-2">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="jenis_reguler" name="jenis_pembayaran" class="custom-control-input" value="reguler" checked>
                                        <label class="custom-control-label" for="jenis_reguler">Reguler (Walk-in Bayar Tunai)</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline d-none d-sm-inline-flex">
                                        <input type="radio" id="jenis_membership" name="jenis_pembayaran" class="custom-control-input" value="membership">
                                        <label class="custom-control-label" for="jenis_membership">Gunakan Membership</label>
                                    </div>
                                    <div class="custom-control custom-radio d-sm-none mt-2">
                                        <input type="radio" id="jenis_membership_mobile" name="jenis_pembayaran" class="custom-control-input" value="membership">
                                        <label class="custom-control-label" for="jenis_membership_mobile">Gunakan Membership</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan Booking</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto fill jam selesai for Edit Modals
    const editJamMulaiSelects = document.querySelectorAll('.jam-mulai-edit');
    editJamMulaiSelects.forEach(select => {
        select.addEventListener('change', function() {
            let jamMulai = this.value;
            let id = this.getAttribute('data-id');
            let selTarget = document.getElementById('jam_selesai_edit_' + id);

            if (jamMulai && selTarget) {
                let parts = jamMulai.split(':');
                let jam = parts[0];
                let menit = parts[1];
                let date = new Date();
                date.setHours(parseInt(jam));
                date.setMinutes(parseInt(menit));

                // default 1 jam (bisa disesuaikan jika durasi > 1 jam didukung UI, namun di snippet pakai +1 jam)
                date.setHours(date.getHours() + 1);

                let jamSelesai = ('0' + date.getHours()).slice(-2) + ':' + ('0' + date.getMinutes()).slice(-2);
                selTarget.value = jamSelesai;
            }
        });
    });

    // Auto fill jam selesai for Create Modal
    document.getElementById('jam_mulai').addEventListener('change', function() {
        let jamMulai = this.value;

        if (jamMulai) {
            let parts = jamMulai.split(':');
            let date = new Date();
            date.setHours(parseInt(parts[0]));
            date.setMinutes(parseInt(parts[1]));

            // default 1 jam
            date.setHours(date.getHours() + 1);

            let jamSelesai = ('0' + date.getHours()).slice(-2) + ':' + ('0' + date.getMinutes()).slice(-2);
            document.getElementById('jam_selesai').value = jamSelesai;
        } else {
            document.getElementById('jam_selesai').value = '';
        }
    });

    // AJAX load filter slot kosong
    function loadJadwal() {
        let lapangan = document.getElementById('lapangan_id').value;
        let tanggal = document.getElementById('tgl_main').value;

        if (lapangan && tanggal) {
            fetch(`/adminfutsal/booking/jadwal-tersedia?lapangan_id=${lapangan}&tanggal=${tanggal}`)
                .then(res => res.json())
                .then(data => {
                    let select = document.getElementById('jam_mulai');
                    select.innerHTML = '<option value="">-- Pilih Jam --</option>';

                    data.forEach(j => {
                        // parse H:i
                        let parts = j.jam_mulai.split(':');
                        let timeString = parts[0] + ':' + parts[1];
                        select.innerHTML += `<option value="${timeString}">${timeString}</option>`;
                    });
                    
                    document.getElementById('jam_selesai').value = '';
                });
        }
    }

    document.getElementById('lapangan_id').addEventListener('change', loadJadwal);
    document.getElementById('tgl_main').addEventListener('change', loadJadwal);
});
</script>
@endsection
