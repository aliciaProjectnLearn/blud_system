@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pembayaran</h1>
        <a href="{{ route('admin.kantin.pembayaran.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">

        {{-- Info Penyewa & Unit --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Penyewa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">Nama Usaha</td>
                            <td>: <strong>{{ $pembayaran->sewaRuko->nama_penyewa ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Lengkap</td>
                            <td>: {{ $pembayaran->sewaRuko->nama_penyewa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. HP</td>
                            <td>: {{ $pembayaran->sewaRuko->no_hp_snapshot ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>: {{ $pembayaran->sewaRuko->penyewa->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Info Sewa --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Sewa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">Kode Unit</td>
                            <td>: <strong>{{ $pembayaran->sewaRuko->ruko->kode_unit ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>: {{ $pembayaran->sewaRuko->ruko->kategori->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Periode Sewa</td>
                            <td>:
                                {{ \Carbon\Carbon::parse($pembayaran->sewaRuko->tanggal_mulai_sewa)->format('d M Y') }} s/d 
                                <br>
                                {{ \Carbon\Carbon::parse($pembayaran->sewaRuko->tanggal_selesai_sewa)->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>No. MOU</td>
                            <td>: {{ $pembayaran->sewaRuko->dokumen->whereIn('tipe_dokumen', ['mou_sistem', 'mou_hardfile'])->first()?->nama_dokumen ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Detail Pembayaran --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembayaran — Termin {{ $pembayaran->termin_ke }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">No. Kwitansi</td>
                            <td>: {{ $pembayaran->no_kwitansi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Jumlah Tagihan</td>
                            <td>: <strong>Rp {{ number_format($pembayaran->jumlah_tagihan, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Jatuh Tempo</td>
                            <td>:
                                {{ $pembayaran->tgl_jatuh_tempo
                                    ? \Carbon\Carbon::parse($pembayaran->tgl_jatuh_tempo)->format('d M Y')
                                    : '-' }}
                                @if($pembayaran->isTerlambat())
                                    <span class="badge badge-danger ml-1">Terlambat</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Tgl Bayar</td>
                            <td>:
                                {{ $pembayaran->tanggal_bayar
                                    ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y')
                                    : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:
                                @if($pembayaran->status_pembayaran === 'dibayar')
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($pembayaran->status_pembayaran === 'menunggu_verifikasi')
                                    <span class="badge badge-info">Menunggu Verifikasi</span>
                                @elseif($pembayaran->status_pembayaran === 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        @if($pembayaran->bukti_pembayaran)
                        <tr>
                            <td>Bukti Bayar</td>
                            <td>: 
                                <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2">
                                    <i class="fas fa-eye mr-1"></i> Lihat Bukti
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

           {{-- Form / Status Pembayaran --}}
            @if($pembayaran->status_pembayaran === 'dibayar')
            <hr>
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-1"></i>
                Pembayaran ini sudah <strong>Lunas</strong>. No. Kwitansi: <strong>{{ $pembayaran->no_kwitansi }}</strong>
            </div>

            @elseif($pembayaran->status_pembayaran === 'menunggu_verifikasi')
            <hr>
            <div class="alert alert-info mb-3">
                <i class="fas fa-clock mr-1"></i>
                @if($pembayaran->tipe_pembayaran_id == 2)
                    Konfirmasi bahwa penyewa telah melakukan pembayaran secara tunai, dan pastikan uang yang diterima sesuai dengan nominal tagihan.
                @else
                    Penyewa sudah mengupload bukti pembayaran. Silakan verifikasi dan konfirmasi sebagai lunas.
                @endif
            </div>
            <h6 class="font-weight-bold text-gray-700 mb-3">Konfirmasi Pembayaran</h6>
            <form id="formKonfirmasiPembayaran" action="{{ route('admin.kantin.pembayaran.update', $pembayaran) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bayar" class="form-control @error('tgl_bayar') is-invalid @enderror"
                                value="{{ old('tgl_bayar', now()->format('Y-m-d')) }}" required>
                            @error('tgl_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipe Pembayaran <span class="text-danger">*</span></label>
                            <select name="tipe_pembayaran_id" class="form-control @error('tipe_pembayaran_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach(\App\Models\TipePembayaran::all() as $tipe)
                                    <option value="{{ $tipe->id }}" {{ old('tipe_pembayaran_id', $pembayaran->tipe_pembayaran_id) == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipe_pembayaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="button" id="btnKonfirmasiLunas" class="btn btn-primary btn-block">
                                <i class="fas fa-check mr-1"></i> Konfirmasi Lunas
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            @elseif($pembayaran->status_pembayaran === 'pending')
            <hr>
            <div class="alert alert-warning">
                <i class="fas fa-hourglass-half mr-1"></i>
                Menunggu penyewa mengupload bukti pembayaran.
            </div>
            @endif
        </div>
    </div>

    {{-- Manajemen Dokumen Pembayaran --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0 font-weight-bold"><i class="fas fa-folder-open mr-2"></i>Manajemen Dokumen</h6>
        </div>
        <div class="card-body">

            @if($pembayaran->status_pembayaran === 'dibayar')
            {{-- Tombol Generate Kwitansi Otomatis (Ini hanya untuk tampilan keseragaman jika sudah ada) --}}
            <div class="mb-3">
                <a href="{{ route('admin.kantin.pembayaran.kwitansi', $pembayaran) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-download mr-2"></i> Download Kwitansi Digital
                </a>
                <small class="text-muted d-block mt-1 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Kwitansi ini otomatis terbuat oleh sistem dan tersimpan
                </small>
            </div>
            <hr>
            @endif

            {{-- Tombol Upload Kwitansi Fisik --}}
            <div class="mb-3">
                <button class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#modalUploadKwitansi">
                    <i class="fas fa-upload mr-2"></i> Upload Kwitansi Fisik
                </button>
                <small class="text-muted d-block mt-1 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Upload scan/foto kwitansi fisik yang sudah diberikan
                </small>
            </div>

            <hr>

            {{-- Daftar Dokumen --}}
            <h6 class="font-weight-bold mb-2">Daftar Dokumen:</h6>
            @php
                $kwitansiDokumen = $pembayaran->sewaRuko->dokumen->filter(function($dok) use ($pembayaran) {
                    return $dok->tipe_dokumen === 'kwitansi_hardfile' || $dok->tipe_dokumen === 'kwitansi_termin_' . $pembayaran->termin_ke;
                });
            @endphp
            @forelse($kwitansiDokumen as $dok)
            <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                <div>
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <span class="font-weight-bold">{{ $dok->nama_dokumen }}</span>
                    <br>
                    <small class="text-muted">
                        Kwitansi
                        @if($dok->tipe_dokumen === 'kwitansi_hardfile')
                            <span class="badge badge-success ml-1">Fisik (Scan)</span>
                        @else
                            <span class="badge badge-info ml-1">Digital</span>
                        @endif
                    </small>
                    <br>
                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($dok->created_at)->format('d M Y H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ asset('storage/' . $dok->path_file) }}" target="_blank" class="btn btn-sm btn-info px-2" title="Lihat">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ asset('storage/' . $dok->path_file) }}" download class="btn btn-sm btn-success px-2" title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                    <button onclick="hapusDokumen({{ $dok->id }})" class="btn btn-sm btn-danger px-2" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-3">
                <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                Belum ada dokumen kwitansi
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Modal Upload Kwitansi Hardfile --}}
<div class="modal fade" id="modalUploadKwitansi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Upload Kwitansi Fisik
                </h5>
                <button type="button" class="close text-white" 
                        data-dismiss="modal">×</button>
            </div>
            <form action="{{ route('admin.kantin.penyewaan.upload-dokumen', $pembayaran->sewa_ruko_id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="tipe_dokumen" value="kwitansi_hardfile">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Nama Dokumen <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_dokumen" class="form-control"
                               value="Kwitansi Fisik Termin {{ $pembayaran->termin_ke }} - {{ $pembayaran->sewaRuko->ruko->kode_unit }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">
                            File Dokumen <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" 
                                   name="file_dokumen"
                                   accept=".pdf,.jpg,.jpeg,.png" required>
                            <label class="custom-file-label">
                                Pilih file...
                            </label>
                        </div>
                        <small class="text-muted">PDF, JPG, PNG — Maks 5MB</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2"
                                  placeholder="Contoh: Kwitansi pembayaran lunas fisik Termin {{ $pembayaran->termin_ke }}...">
                        </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" 
                            data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload mr-1"></i> Upload Kwitansi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('btnKonfirmasiLunas')
    ?.addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        html: 'Tandai pembayaran ini sebagai <strong>LUNAS</strong>?<br><small class="text-muted">Kwitansi akan digenerate otomatis.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Konfirmasi',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formKonfirmasiPembayaran').submit();
        }
    });
});

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

// Custom File Input
$('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
});
</script>
@endpush

@endsection
