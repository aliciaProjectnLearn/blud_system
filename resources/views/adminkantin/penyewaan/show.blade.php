@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Penyewaan: {{ $data->ruko->kode_unit }}</h1>
        <div>
            <a href="{{ route('admin.kantin.penyewaan.index') }}" class="btn btn-secondary btn-sm shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
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
        {{-- Kolom Data Penyewa --}}
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">👤 Data Penyewa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="35%">Nama</td>
                            <td>: <strong>{{ $data->nama_penyewa }}</strong></td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>: {{ $data->nik_penyewa ? substr($data->nik_penyewa, 0, 4) . '****' . substr($data->nik_penyewa, -4) : '-' }}</td>
                        </tr>
                        <tr>
                            <td>No HP</td>
                            <td>: {{ $data->no_hp_snapshot ? substr($data->no_hp_snapshot, 0, 4) . '****' . substr($data->no_hp_snapshot, -4) : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        {{-- Kolom Data Ruko --}}
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🏪 Data Unit Ruko</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="35%">Kode</td>
                                    <td>: <strong>{{ $data->ruko->kode_unit }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Jenis</td>
                                    <td>: {{ $data->ruko->kategori->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Harga</td>
                                    <td>: Rp {{ number_format($data->ruko->harga, 0, ',', '.') }}/thn</td>
                                </tr>
                                <tr>
                                    <td>Status Unit</td>
                                    <td>: 
                                        <span class="badge {{ $data->ruko->status_unit == 'terisi' ? 'badge-danger' : 'badge-success' }}">
                                            {{ ucfirst($data->ruko->status_unit) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4 text-center">
                            @if($data->ruko->foto_ruko)
                                <img src="{{ asset('storage/' . $data->ruko->foto_ruko) }}" alt="Foto Unit" class="img-fluid rounded border">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded border" style="height: 100px;">
                                    <span class="text-muted small">No Image</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Periode & Pembayaran --}}
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📅 Periode & Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Mulai:</strong> {{ $data->tanggal_mulai_sewa ? \Carbon\Carbon::parse($data->tanggal_mulai_sewa)->format('d M Y') : '-' }}</p>
                            <p class="mb-0"><strong>Selesai:</strong> {{ $data->tanggal_selesai_sewa ? \Carbon\Carbon::parse($data->tanggal_selesai_sewa)->format('d M Y') : '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Status Sewa:</strong> 
                                @php
                                    $badgeClass = [
                                        'pending'    => 'badge-warning',
                                        'disetujui'  => 'badge-info',
                                        'ditolak'    => 'badge-danger',
                                        'aktif'      => 'badge-success',
                                        'selesai'    => 'badge-secondary',
                                        'dibatalkan' => 'badge-danger',
                                    ][$data->status_sewa] ?? 'badge-dark';
                                    
                                    $statusLabel = [
                                        'pending'    => 'Menunggu Verifikasi',
                                        'disetujui'  => 'Disetujui',
                                        'ditolak'    => 'Ditolak',
                                        'aktif'      => 'Aktif',
                                        'selesai'    => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                    ][$data->status_sewa] ?? ucfirst($data->status_sewa);
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            </p>
                            <p class="mb-0"><strong>Skema:</strong> {{ $data->pembayaran->count() > 0 ? $data->pembayaran->count() . ' Termin' : '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0"><strong>Total Biaya:</strong> Rp {{ number_format($data->ruko->harga, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($data->pembayaran->count() > 0)
                    <div class="row mt-4">
                        @foreach($data->pembayaran as $bayar)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-{{ $bayar->status_pembayaran === 'dibayar' ? 'success' : 'warning' }}">
                                <div class="card-body py-2">
                                    <h6 class="font-weight-bold text-primary mb-1">Termin {{ $bayar->termin_ke }}</h6>
                                    <h5 class="mb-2">Rp {{ number_format($bayar->jumlah_tagihan, 0, ',', '.') }}</h5>
                                    <div class="text-sm text-muted mb-2">
                                        Jatuh tempo: {{ $bayar->tgl_jatuh_tempo ? \Carbon\Carbon::parse($bayar->tgl_jatuh_tempo)->format('d M Y') : '-' }}
                                    </div>
                                    <div>
                                        @if($bayar->status_pembayaran === 'dibayar')
                                            <span class="badge badge-success">Lunas</span>
                                        @elseif($bayar->status_pembayaran === 'menunggu_verifikasi')
                                            <span class="badge badge-info">Menunggu Verifikasi</span>
                                        @elseif($bayar->status_pembayaran === 'pending')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @else
                                            <span class="badge badge-danger">{{ ucfirst($bayar->status_pembayaran) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-light mt-3">Belum ada tagihan termin di-generate.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Dokumen --}}
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-folder-open mr-2"></i>Manajemen Dokumen</h6>
                </div>
                <div class="card-body">

                    {{-- Tombol Generate MOU --}}
                    <div class="mb-3">
                        <a href="{{ route('admin.kantin.penyewaan.generate-mou', $data->id) }}"
                           class="btn btn-primary btn-block">
                            <i class="fas fa-magic mr-2"></i> Generate MOU Otomatis
                        </a>
                        <small class="text-muted d-block mt-1 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            MOU akan otomatis tersimpan ke daftar dokumen
                        </small>
                    </div>

                    <hr>

                    {{-- Tombol Upload MOU Hardfile --}}
                    <div class="mb-3">
                        <button class="btn btn-outline-primary btn-block" 
                                data-toggle="modal" data-target="#modalUploadHardfile">
                            <i class="fas fa-upload mr-2"></i> Upload MOU Hardfile
                        </button>
                        <small class="text-muted d-block mt-1 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Upload scan/foto MOU fisik yang sudah ditandatangani
                        </small>
                    </div>

                    <hr>

                    {{-- Daftar Dokumen --}}
                    <h6 class="font-weight-bold mb-2">Daftar Dokumen:</h6>
                    @forelse($data->dokumen ?? [] as $dok)
                    <div class="d-flex justify-content-between align-items-center 
                                border rounded p-2 mb-2">
                        <div>
                            <i class="fas fa-file-pdf text-danger mr-2"></i>
                            <span class="font-weight-bold">{{ $dok->nama_dokumen }}</span>
                            <br>
                            <small class="text-muted">
                                {{ ucfirst(str_replace('_',' ',$dok->tipe_dokumen)) }}
                                @if($dok->tipe_dokumen === 'mou_hardfile')
                                    <span class="badge badge-success ml-1">Ditandatangani</span>
                                @endif
                                @if($dok->tipe_dokumen === 'mou_sistem')
                                    <span class="badge badge-info ml-1">Digital</span>
                                @endif
                            </small>
                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($dok->created_at)->format('d M Y H:i') }}
                            </small>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ asset('storage/' . $dok->path_file) }}" 
                               target="_blank"
                               class="btn btn-sm btn-info px-2" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ asset('storage/' . $dok->path_file) }}" 
                               download
                               class="btn btn-sm btn-success px-2" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                            <button onclick="hapusDokumen({{ $dok->id }})"
                                    class="btn btn-sm btn-danger px-2" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                        Belum ada dokumen
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kolom Riwayat Aktivitas --}}
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📋 Riwayat Aktivitas</h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted">
                        <em>Fitur riwayat perubahan status akan segera hadir.</em>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Upload MOU Hardfile --}}
<div class="modal fade" id="modalUploadHardfile" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-upload mr-2"></i>Upload MOU Hardfile
                </h5>
                <button type="button" class="close text-white" 
                        data-dismiss="modal">×</button>
            </div>
            <form action="{{ route('admin.kantin.penyewaan.upload-dokumen', $data->id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    {{-- Panduan Upload --}}
                    <div class="alert alert-info mb-3">
                        <h6 class="font-weight-bold">
                            <i class="fas fa-info-circle mr-1"></i> Panduan Upload MOU Hardfile
                        </h6>
                        <ol class="mb-0 pl-3 small">
                            <li>Pastikan MOU fisik sudah <strong>ditandatangani</strong> 
                                oleh kedua pihak</li>
                            <li>Scan atau foto dokumen dengan <strong>kualitas jelas</strong></li>
                            <li>Format yang diterima: <strong>PDF, JPG, PNG</strong></li>
                            <li>Ukuran maksimal: <strong>5MB</strong></li>
                            <li>Dokumen ini akan bisa diakses oleh penyewa 
                                melalui link token mereka</li>
                        </ol>
                    </div>

                    <input type="hidden" name="tipe_dokumen" value="mou_hardfile">

                    <div class="form-group">
                        <label class="font-weight-bold">
                            Nama Dokumen <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_dokumen" class="form-control"
                               value="MOU Hardfile - {{ $data->ruko->kode_unit }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">
                            File Dokumen <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" 
                                   name="file_dokumen" id="fileDokumen"
                                   accept=".pdf,.jpg,.jpeg,.png" required>
                            <label class="custom-file-label" for="fileDokumen">
                                Pilih file...
                            </label>
                        </div>
                        <small class="text-muted">PDF, JPG, PNG — Maks 5MB</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2"
                                  placeholder="Contoh: MOU asli sudah ditandatangani tanggal...">
                        </textarea>
                    </div>

                    {{-- Preview --}}
                    <div id="preview-wrapper" class="d-none text-center mt-2">
                        <img id="img-preview" src="" class="img-fluid rounded" 
                             style="max-height:200px">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" 
                            data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload mr-1"></i> Upload Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if(session('download_url'))
        window.location.href = "{{ session('download_url') }}";
    @endif

    function setujui(id) { updateStatus(id, 'disetujui'); }
    function tolak(id) { updateStatus(id, 'ditolak'); }
    function aktifkan(id) { updateStatus(id, 'aktif'); }

    function hapusDokumen(id) {
        if(!confirm('Apakah anda yakin ingin menghapus dokumen ini?')) return;
        
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ url('admin/kantin/dokumen') }}/" + id;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }

    // Custom File Input & Preview
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
        
        // Preview image if it's an image
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            let fileType = this.files[0].type;
            
            if (fileType.startsWith('image/')) {
                reader.onload = function(e) {
                    $('#img-preview').attr('src', e.target.result);
                    $('#preview-wrapper').removeClass('d-none');
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                $('#preview-wrapper').addClass('d-none');
            }
        }
    });

    function updateStatus(id, status) {
        if(!confirm('Apakah anda yakin mengubah status menjadi: ' + status + '?')) return;
        
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ url('admin/kantin/penyewaan') }}/" + id;
        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="status_sewa" value="${status}">
            <input type="hidden" name="tanggal_mulai_sewa" value="{{ $data->tanggal_mulai_sewa ? \Carbon\Carbon::parse($data->tanggal_mulai_sewa)->format('Y-m-d') : date('Y-m-d') }}">
            <input type="hidden" name="tanggal_selesai_sewa" value="{{ $data->tanggal_selesai_sewa ? \Carbon\Carbon::parse($data->tanggal_selesai_sewa)->format('Y-m-d') : date('Y-m-d', strtotime('+1 year')) }}">
            <input type="hidden" name="harga_sewa_tahunan" value="{{ $data->harga_sewa_tahunan ?? $data->ruko->harga ?? 0 }}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
