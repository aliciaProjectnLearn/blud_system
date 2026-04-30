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
                            <td class="text-muted">NIK</td>
                            <td>: {{ $pembayaran->sewaRuko->nik_penyewa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>: -</td>
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
                            <td>: {{ $pembayaran->sewaRuko->dokumen->first()?->no_mou ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Detail Pembayaran --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembayaran — Termin {{ $pembayaran->termin_ke }}</h6>
            @if($pembayaran->status_pembayaran === 'dibayar')
                <a href="{{ route('admin.kantin.pembayaran.kwitansi', $pembayaran) }}"
                    class="btn btn-success btn-sm">
                    <i class="fas fa-download mr-1"></i> Download Kwitansi
                </a>
            @endif
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
</script>
@endpush

@endsection
