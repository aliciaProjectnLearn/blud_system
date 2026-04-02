@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pembayaran</h1>
        <a href="{{ route('adminkantin.pembayaran.index') }}" class="btn btn-secondary btn-sm">
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
                            <td>: <strong>{{ $pembayaran->sewaRuko->penyewa->nama_usaha ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Nama Pemilik</td>
                            <td>: {{ $pembayaran->sewaRuko->penyewa->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>: {{ $pembayaran->sewaRuko->penyewa->user->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
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
                                {{ \Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_mulai)->format('d M Y') }}
                                s/d
                                {{ \Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_selesai)->format('d M Y') }}
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
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembayaran — Termin {{ $pembayaran->termin }}</h6>
            @if($pembayaran->status === 'verifikasi')
                <a href="{{ route('adminkantin.pembayaran.kwitansi', $pembayaran) }}"
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
                                {{ $pembayaran->tgl_bayar
                                    ? \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d M Y')
                                    : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:
                                @if($pembayaran->status === 'verifikasi')
                                    <span class="badge badge-success">Terverifikasi</span>
                                @elseif($pembayaran->status === 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @else
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Form Input Pembayaran --}}
            @if($pembayaran->status !== 'verifikasi')
            <hr>
            <h6 class="font-weight-bold text-gray-700 mb-3">Konfirmasi Pembayaran</h6>
            <form action="{{ route('adminkantin.pembayaran.update', $pembayaran) }}" method="POST">
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
                                    <option value="{{ $tipe->id }}" {{ old('tipe_pembayaran_id') == $tipe->id ? 'selected' : '' }}>
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
                            <button type="submit" class="btn btn-primary btn-block"
                                onclick="return confirm('Konfirmasi pembayaran ini?')">
                                <i class="fas fa-check mr-1"></i> Konfirmasi Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>

</div>
@endsection
