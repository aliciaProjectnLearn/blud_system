@extends('layouts.app')
@section('title', 'Riwayat Pembayaran')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Pembayaran</h1>
        <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Status Pengajuan Sewa</h6>
        </div>
        <div class="card-body">
            @if(isset($pengajuan) && $pengajuan->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted mb-0">Belum ada pengajuan sewa.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Unit</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Durasi Sewa</th>
                            <th>Status Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengajuan as $p)
                        @php
                            $badgeP = match($p->status) {
                                'aktif'      => 'success',
                                'pending'    => 'warning',
                                'ditolak'    => 'danger',
                                'selesai'    => 'secondary',
                                'dibatalkan' => 'dark',
                                default      => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $p->ruko->kode_unit ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d M Y') }} s.d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d M Y') }}</td>
                            <td><span class="badge badge-{{ $badgeP }}">{{ ucfirst($p->status) }}</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalDetailPengajuan-{{ $p->id }}">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Riwayat Pembayaran</h6>
        </div>
        <div class="card-body">
            @if($riwayat->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada riwayat pembayaran.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Termin</th>
                            <th>Jumlah Tagihan</th>
                            <th>Tanggal Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $r)
                        @php
                            $badge = match($r->status) {
                                'lunas'      => 'success',
                                'verifikasi' => 'info',
                                'menunggu'   => 'warning',
                                default      => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $r->sewaRuko->ruko->kode_unit ?? '-' }}</td>
                            <td>Termin {{ $r->termin }}</td>
                            <td>Rp {{ number_format($r->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>{{ $r->tgl_bayar ? \Carbon\Carbon::parse($r->tgl_bayar)->format('d M Y') : '-' }}</td>
                            <td><span class="badge badge-{{ $badge }}">{{ ucfirst($r->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals for Detail Pengajuan --}}
@if(isset($pengajuan) && $pengajuan->isNotEmpty())
    @foreach($pengajuan as $p)
    <div class="modal fade" id="modalDetailPengajuan-{{ $p->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-contract mr-2"></i>Detail Pengajuan Sewa: {{ $p->ruko->kode_unit ?? '-' }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2">Informasi Penyewa</h6>
                            <p class="mb-1 text-sm"><strong>Nama Pemohon:</strong> {{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</p>
                            <p class="mb-1 text-sm"><strong>NIK:</strong> {{ auth()->user()->nik ?? '-' }}</p>
                            <p class="mb-1 text-sm"><strong>Nama Usaha:</strong> {{ $p->penyewa->nama_usaha ?? '-' }}</p>
                            <p class="mb-1 text-sm"><strong>Alamat:</strong> {{ $p->penyewa->alamat ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2">Detail Unit Ruko</h6>
                            <p class="mb-1 text-sm"><strong>Kode:</strong> {{ $p->ruko->kode_unit ?? '-' }}</p>
                            <p class="mb-1 text-sm"><strong>Kategori:</strong> {{ $p->ruko->kategori->nama ?? '-' }}</p>
                            <p class="mb-1 text-sm"><strong>Harga Sewa:</strong> Rp {{ number_format($p->harga_sewa_tahunan, 0, ',', '.') }} / Tahun</p>
                            <p class="mb-1 text-sm"><strong>Status Saat Ini:</strong> <span class="badge badge-{{ match($p->status) { 'aktif' => 'success', 'pending' => 'warning', 'ditolak' => 'danger', 'selesai' => 'secondary', 'dibatalkan' => 'dark', default => 'secondary' } }}">{{ ucfirst($p->status) }}</span></p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2">Dokumen Terlampir</h6>
                            @if($p->dokumen && $p->dokumen->count() > 0)
                                <ul class="list-group list-group-flush small">
                                    @foreach($p->dokumen as $doc)
                                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                            <div>
                                                <i class="fas fa-paperclip text-muted mr-1"></i> 
                                                <strong>{{ $doc->nama_dokumen }}</strong><br>
                                                <span class="text-muted" style="font-size:0.8rem">No MOU: {{ $doc->no_mou ?? '-' }}</span>
                                            </div>
                                            <span class="badge badge-light border"><i class="fas fa-check text-success"></i> Tersimpan</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted text-sm mb-0">Belum ada dokumen yang dilampirkan atau digenerate oleh sistem.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection
