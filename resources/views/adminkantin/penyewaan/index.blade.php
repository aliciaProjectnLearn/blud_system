@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Penyewaan</h1>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Pencarian</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.kantin.penyewaan.index') }}" method="GET" class="row">
                <div class="col-6 col-md-3 mb-3">
                    <label>Status Sewa</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label>Unit Ruko</label>
                    <select name="ruko_id" class="form-control">
                        <option value="">Semua Unit</option>
                        @foreach($rukos as $ruko)
                            <option value="{{ $ruko->id }}" {{ request('ruko_id') == $ruko->id ? 'selected' : '' }}>
                                {{ $ruko->kode_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label>Nama Penyewa</label>
                    <select name="penyewa_id" class="form-control">
                        <option value="">Semua Penyewa</option>
                        @foreach($penyewas as $p)
                            <option value="{{ $p->id }}" {{ request('penyewa_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->user->nama_lengkap ?? $p->nama_usaha }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Cari</button>
                    <a href="{{ route('admin.kantin.penyewaan.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Penyewaan Ruko / Kantin</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th class="d-none d-sm-table-cell">No</th>
                            <th>Nama Penyewa</th>
                            <th>Kode Unit</th>
                            <th class="d-none d-md-table-cell">Jenis Unit</th>
                            <th>Tgl Mulai</th>
                            <th class="d-none d-md-table-cell">Tgl Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                            <td>{{ $item->nama_penyewa }}</td>
                            <td>{{ $item->ruko->kode_unit }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->ruko->kategori->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai_sewa)->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">{{ \Carbon\Carbon::parse($item->tanggal_selesai_sewa)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'pending'    => 'badge-warning',
                                        'disetujui'  => 'badge-info',
                                        'aktif'      => 'badge-success',
                                        'selesai'    => 'badge-secondary',
                                        'dibatalkan' => 'badge-danger',
                                    ][$item->status_sewa] ?? 'badge-dark';
                                    
                                    $statusLabel = [
                                        'pending'    => 'Menunggu Verifikasi',
                                        'disetujui'  => 'Disetujui',
                                        'aktif'      => 'Aktif',
                                        'selesai'    => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                    ][$item->status_sewa] ?? ucfirst($item->status_sewa);
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.kantin.penyewaan.show', $item->id) }}" class="btn btn-info btn-sm px-2" 
                                       title="Detail" data-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-warning btn-sm px-2"
                                            title="Edit" data-toggle="tooltip"
                                            onclick="editPenyewaan({{ $item->id }}, '{{ $item->tanggal_mulai_sewa ? \Carbon\Carbon::parse($item->tanggal_mulai_sewa)->format('Y-m-d') : '' }}', '{{ $item->tanggal_selesai_sewa ? \Carbon\Carbon::parse($item->tanggal_selesai_sewa)->format('Y-m-d') : '' }}', '{{ $item->status_sewa }}', {{ (int)($item->harga_sewa_tahunan ?? $item->ruko->harga ?? 0) }})">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if($item->status_sewa == 'dibatalkan')
                                    <button class="btn btn-danger btn-sm px-2"
                                            title="Hapus" data-toggle="tooltip"
                                            onclick="hapusPenyewaan({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Dipindah ke Luar Loop -->
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data penyewaan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal Global -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Penyewaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai_sewa" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai_sewa" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Sewa Tahunan</label>
                        <input type="text" id="edit_total_biaya_display" class="form-control" readonly style="background:#f8f9fc">
                        <input type="hidden" id="edit_total_biaya" name="harga_sewa_tahunan">
                        <small class="text-muted">Otomatis dari harga unit yang disewa</small>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="edit_status" name="status_sewa" class="form-control" required>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() { 
        $('[data-toggle="tooltip"]').tooltip(); 
    });

    function editPenyewaan(id, tglMulai, tglSelesai, status, harga) {
        document.getElementById('editForm').action = "{{ url('admin/kantin/penyewaan') }}/" + id;
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_tanggal_mulai').value = tglMulai;
        document.getElementById('edit_tanggal_selesai').value = tglSelesai;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_total_biaya').value = harga;
        document.getElementById('edit_total_biaya_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga);
        $('#modalEdit').modal('show');
    }

    function hapusPenyewaan(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data penyewaan akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ url('admin/kantin/penyewaan') }}/" + id;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
