@extends('layouts.app')

@section('title', 'Detail Penyewaan - ' . $sewa->ruko->kode_unit)

@push('styles')
<style>
    .status-card {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }
    .status-header {
        padding: 24px;
        color: white;
    }
    .status-active { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
    .status-pending { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
    .status-expired { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); }
    
    .timeline-steps { display: flex; justify-content: center; flex-wrap: wrap; }
    .timeline-steps .timeline-step { align-items: center; display: flex; flex-direction: column; position: relative; margin: 1rem; }
    .timeline-steps .timeline-content { width: 10rem; text-align: center; }
    .timeline-steps .timeline-content .inner-circle { border-radius: 1.5rem; height: 1rem; width: 1rem; margin: 0 auto; background-color: #4e73df; position: relative; z-index: 1; }
    .timeline-steps .timeline-content .inner-circle:before { content: ""; background-color: #4e73df; display: inline-block; height: 3rem; width: 3rem; min-width: 3rem; border-radius: 6.25rem; opacity: .1; position: absolute; top: -1rem; left: -1rem; }
    .timeline-steps .timeline-step:not(:last-child):after { content: ""; display: block; border-top: .25rem dotted #e3e6f0; width: 3.46rem; position: absolute; left: 7.5rem; top: .3125rem; }
    
    .payment-card {
        border-left: 5px solid #4e73df;
        transition: all 0.2s;
    }
    .payment-card.paid { border-left-color: #1cc88a; }
    .payment-card.pending { border-left-color: #f6c23e; }
    
    .upload-zone {
        border: 2px dashed #d1d3e2;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        background: #f8f9fc;
        cursor: pointer;
        transition: all 0.2s;
    }
    .upload-zone:hover { border-color: #4e73df; background: #eaecf4; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Status --}}
    <div class="card status-card shadow mb-4 animate__animated animate__fadeInDown">
        @php
            $statusClass = 'status-pending';
            if($sewa->status_sewa == 'aktif') $statusClass = 'status-active';
            if($sewa->status_sewa == 'selesai') $statusClass = 'status-expired';
        @endphp
        <div class="status-header {{ $statusClass }}">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="text-white-50 small text-uppercase font-weight-bold mb-1">Status Penyewaan</h5>
                    <h2 class="font-weight-bold mb-0">
                        {{ strtoupper($sewa->status_sewa) }} 
                        <span class="mx-2 opacity-50">|</span> 
                        {{ $sewa->ruko->nama_ruko }}
                    </h2>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <div class="h5 mb-0 font-weight-bold">Token: {{ $sewa->access_token }}</div>
                    <div class="small text-white-50">Gunakan token ini untuk akses cepat</div>
                </div>
            </div>
        </div>
        <div class="card-body bg-white py-4">
            <div class="row text-center">
                <div class="col-md-4 border-right">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mulai Sewa</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Carbon\Carbon::parse($sewa->tanggal_mulai_sewa)->format('d M Y') }}</div>
                </div>
                <div class="col-md-4 border-right">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Berakhir Pada</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Carbon\Carbon::parse($sewa->tanggal_selesai_sewa)->format('d M Y') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Penyewa</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sewa->nama_penyewa }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Progress Pembayaran --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tagihan & Riwayat Pembayaran</h6>
                </div>
                <div class="card-body">
                    @foreach($sewa->pembayaran as $p)
                    <div class="card payment-card shadow-sm mb-3 {{ $p->status_pembayaran == 'dibayar' ? 'paid' : 'pending' }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Termin ke-{{ $p->termin_ke }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($p->jumlah_tagihan, 0, ',', '.') }}
                                    </div>
                                    <div class="small mt-1">
                                        @if($p->status_pembayaran == 'dibayar')
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>
                                            <span class="text-muted ml-2">Dibayar pada: {{ Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') }}</span>
                                        @elseif($p->status_pembayaran == 'pending' && $p->bukti_pembayaran)
                                            <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> MENUNGGU VERIFIKASI</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i> BELUM DIBAYAR</span>
                                            <span class="text-muted ml-2">Jatuh tempo: {{ Carbon\Carbon::parse($p->tgl_jatuh_tempo)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if($p->status_pembayaran != 'dibayar' && !($p->status_pembayaran == 'pending' && $p->bukti_pembayaran))
                                        <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="openUploadModal({{ $p->id }}, {{ $p->jumlah_tagihan }})">
                                            <i class="fas fa-upload mr-1"></i> Bayar Sekarang
                                        </button>
                                    @elseif($p->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                            <i class="fas fa-image mr-1"></i> Lihat Bukti
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    {{-- Informasi Rekening --}}
                    <div class="alert alert-light border mt-4">
                        <h6 class="font-weight-bold text-dark"><i class="fas fa-university mr-2"></i>Instruksi Pembayaran Transfer:</h6>
                        <p class="small mb-2">Silakan transfer sesuai nominal tagihan ke rekening berikut:</p>
                        <div class="bg-white p-3 rounded border">
                            <div class="row">
                                <div class="col-6">
                                    <span class="text-xs text-muted d-block">Bank</span>
                                    <strong>BANK BJB</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-xs text-muted d-block">Nomor Rekening</span>
                                    <strong>0083210456789</strong>
                                </div>
                                <div class="col-12 mt-2">
                                    <span class="text-xs text-muted d-block">Atas Nama</span>
                                    <strong>BLUD SMK NEGERI 1 CIREBON</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Sewa Lain --}}
            @if($riwayat->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Riwayat Penyewaan Lain</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Unit</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayat as $r)
                                <tr>
                                    <td>{{ $r->ruko->kode_unit }}</td>
                                    <td>{{ Carbon\Carbon::parse($r->tanggal_mulai_sewa)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($r->tanggal_selesai_sewa)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $r->status_sewa == 'aktif' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $r->status_sewa }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('user.kantin.sewa.token', ['token' => $r->access_token]) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Detail Unit --}}
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Unit</h6>
                </div>
                <div class="card-body p-0">
                    <img src="{{ $sewa->ruko->foto_ruko_url }}" class="img-fluid w-100" style="height: 200px; object-fit: cover;" alt="Foto Ruko">
                    <div class="p-3">
                        <h5 class="font-weight-bold text-dark">{{ $sewa->ruko->nama_ruko }}</h5>
                        <p class="text-muted small mb-3">{{ $sewa->ruko->kategori->nama }}</p>
                        
                        <div class="row no-gutters mb-2">
                            <div class="col-4 text-xs font-weight-bold text-uppercase text-muted">Kode Unit</div>
                            <div class="col-8 text-sm font-weight-bold">{{ $sewa->ruko->kode_unit }}</div>
                        </div>
                        <div class="row no-gutters mb-2">
                            <div class="col-4 text-xs font-weight-bold text-uppercase text-muted">Ukuran</div>
                            <div class="col-8 text-sm font-weight-bold">{{ $sewa->ruko->ukuran_ruko ?? '-' }}</div>
                        </div>
                        <div class="row no-gutters mb-2">
                            <div class="col-4 text-xs font-weight-bold text-uppercase text-muted">Biaya Sewa</div>
                            <div class="col-8 text-sm font-weight-bold text-primary">Rp {{ number_format($sewa->harga_sewa_tahunan, 0, ',', '.') }}/thn</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bantuan --}}
            <div class="card shadow mb-4 bg-primary text-white border-0">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3"><i class="fas fa-headset mr-2"></i>Butuh Bantuan?</h6>
                    <p class="small mb-3">Jika Anda mengalami kendala pembayaran atau akses, hubungi admin melalui WhatsApp:</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-light btn-block btn-sm font-weight-bold">
                        <i class="fab fa-whatsapp mr-1"></i> WhatsApp Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload Bukti --}}
<div class="modal fade" id="modalUploadBukti" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('user.kantin.sewa.upload-bukti', ['token' => $sewa->access_token]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pembayaran_id" id="modal_pembayaran_id">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Unggah Bukti Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">Nominal Transfer: <br><strong class="h4 text-primary" id="modal_nominal">Rp 0</strong></p>
                    
                    <div class="upload-zone" onclick="document.getElementById('bukti_file').click()">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <p class="mb-0 text-muted" id="file_status">Klik untuk pilih file bukti transfer (JPG, PNG, PDF)</p>
                        <input type="file" name="bukti_pembayaran" id="bukti_file" class="d-none" accept="image/*,.pdf" onchange="updateFileLabel(this)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow">Kirim Bukti</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openUploadModal(id, nominal) {
        $('#modal_pembayaran_id').val(id);
        $('#modal_nominal').text('Rp ' + new Intl.NumberFormat('id-ID').format(nominal));
        $('#modalUploadBukti').modal('show');
    }

    function updateFileLabel(input) {
        if (input.files && input.files[0]) {
            $('#file_status').text(input.files[0].name).addClass('text-primary font-weight-bold');
        }
    }
</script>
@endpush
@endsection
