@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Penyewaan: {{ $data->ruko->kode_unit }}</h1>
        <a href="{{ route('admin.kantin.penyewaan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="row">
        <!-- Data Penyewa & Unit -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Utama</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Data Penyewa</h5>
                            <hr>
                            <p><b>Nama:</b> {{ $data->penyewa->user->nama_lengkap ?? '-' }}</p>
                            <p><b>Nama Usaha:</b> {{ $data->penyewa->nama_usaha }}</p>
                            <p><b>NIK:</b> {{ $data->penyewa->user->nik ?? '-' }}</p>
                            <p><b>Alamat:</b> {{ $data->penyewa->alamat }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Data Unit Ruko</h5>
                            <hr>
                            <p><b>Kode Unit:</b> {{ $data->ruko->kode_unit }}</p>
                            <p><b>Jenis Unit:</b> {{ $data->ruko->kategori->nama ?? '-' }}</p>
                            <p><b>Status Unit:</b> 
                                <span class="badge {{ $data->ruko->status_unit == 'terisi' ? 'badge-danger' : 'badge-success' }}">
                                    {{ ucfirst($data->ruko->status_unit) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Periode & Biaya</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><b>Tanggal Mulai:</b> {{ \Carbon\Carbon::parse($data->tgl_mulai)->format('d F Y') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><b>Tanggal Selesai:</b> {{ \Carbon\Carbon::parse($data->tgl_selesai)->format('d F Y') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><b>Status Sewa:</b> 
                                        @if($data->status == 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($data->status == 'selesai')
                                            <span class="badge badge-secondary">Selesai</span>
                                        @else
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <p><b>Total Biaya Tahunan:</b> Rp {{ number_format($data->harga_sewa_tahunan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions/Info -->
        <div class="col-lg-4">
            <!-- Dokumen Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Manajemen Dokumen</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary btn-block mb-3" data-toggle="modal" data-target="#uploadModal">
                        <i class="fas fa-upload fa-sm"></i> Upload Dokumen Baru
                    </button>
                    
                    <a href="{{ route('admin.kantin.penyewaan.generateMOU', $data->id) }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-file-contract fa-sm"></i> Generate MOU Otomatis
                    </a>

                    <hr>
                    
                    <h6>Daftar Dokumen:</h6>
                    <div class="list-group list-group-flush">
                        @forelse($data->dokumen as $doc)
                            <div class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">No MOU: {{ $doc->no_mou }}</small>
                                        <div class="font-weight-bold text-truncate" style="max-width: 150px;">{{ $doc->nama_dokumen }}</div>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.kantin.penyewaan.downloadDokumen', $doc->id) }}" class="btn btn-sm btn-link text-success" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('admin.kantin.penyewaan.hapusDokumen', $doc->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Hapus dokumen ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">Belum ada dokumen yang diunggah.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.kantin.penyewaan.uploadDokumen', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumen Penyewaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Dokumen</label>
                        <input type="text" name="nama_dokumen" class="form-control" placeholder="Contoh: MOU Sewa Ruko A1" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih File</label>
                        <input type="file" name="file" class="form-control-file" required>
                        <small class="text-muted">Format: PDF, DOC, JPG, PNG (Max 5MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Mulai Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if(session('download_url'))
        // Buka link download di tab baru atau langsung download tanpa pindah halaman
        window.location.href = "{{ session('download_url') }}";
    @endif
</script>
@endpush
