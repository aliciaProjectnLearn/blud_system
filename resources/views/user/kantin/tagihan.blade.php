@extends('layouts.app')
@section('title', 'Tagihan Pembayaran')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Tagihan Pembayaran</h1>
        <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    {{-- Banner Peringatan Termin 1 --}}
    @php
        $terminSatuBelumBayar = $tagihan->where('termin', 1)
            ->whereIn('status', ['menunggu'])->first();
    @endphp

    @if($terminSatuBelumBayar)
    <div class="alert alert-warning border-left-warning shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x text-warning mr-3"></i>
            <div>
                <h6 class="font-weight-bold mb-1">Aktivasi Unit Diperlukan</h6>
                <p class="mb-0">
                    Bayarkan <strong>Termin 1</strong> untuk mengaktifkan unit 
                    <strong>{{ $terminSatuBelumBayar->sewaRuko->ruko->kode_unit ?? '' }}</strong> 
                    yang Anda sewa. Unit tidak akan aktif sebelum pembayaran Termin 1 
                    diverifikasi oleh admin.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabel: Daftar Tagihan Aktif --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tagihan Aktif & Termin</h6>
        </div>
        <div class="card-body">
            @if($tagihan->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-gray-200 mb-3"></i>
                    <p class="text-muted">Tidak ada tagihan aktif saat ini.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Termin</th>
                            <th>Jumlah Tagihan</th>
                            <th class="d-none d-md-table-cell">Jatuh Tempo</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tagihan as $r)
                        @php
                            $badge = match($r->status) {
                                'verifikasi' => 'info',
                                'menunggu'   => 'warning',
                                default      => 'secondary',
                            };
                            $statusText = match($r->status) {
                                'verifikasi' => 'Menunggu Verifikasi',
                                'menunggu'   => 'Menunggu Pembayaran',
                                default      => ucfirst($r->status),
                            };
                        @endphp
                        <tr>
                            <td>{{ $r->sewaRuko->ruko->kode_unit ?? '-' }}</td>
                            <td>Termin {{ $r->termin }}</td>
                            <td>Rp {{ number_format($r->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="d-none d-md-table-cell">{{ $r->tgl_jatuh_tempo ? \Carbon\Carbon::parse($r->tgl_jatuh_tempo)->format('d M Y') : '-' }}</td>
                            <td><span class="badge badge-{{ $badge }}">{{ $statusText }}</span></td>
                            <td class="text-center">
                                @if($r->status === 'menunggu')
                                    <button type="button" class="btn btn-sm btn-primary"
                                        data-toggle="modal"
                                        data-target="#modalUpload-{{ $r->id }}">
                                        <i class="fas fa-upload mr-1"></i> Bayar Sekarang
                                    </button>
                                @elseif($r->status === 'verifikasi')
                                    <span class="text-info small">
                                        <i class="fas fa-clock mr-1"></i> Bukti sedang ditinjau admin
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Upload Bukti Pembayaran --}}
@if(isset($tagihan) && $tagihan->isNotEmpty())
    @foreach($tagihan as $r)
        @if($r->status === 'menunggu')
        <div class="modal fade" id="modalUpload-{{ $r->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-upload mr-2"></i>Upload Bukti Pembayaran
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('user.kantin.confirm_pembayaran', $r->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info text-sm">
                                <strong>Termin {{ $r->termin }}</strong> — 
                                Rp {{ number_format($r->jumlah_tagihan, 0, ',', '.') }}<br>
                                Jatuh tempo: {{ $r->tgl_jatuh_tempo ? \Carbon\Carbon::parse($r->tgl_jatuh_tempo)->format('d M Y') : '-' }}
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Tipe Pembayaran <span class="text-danger">*</span></label>
                                <select name="tipe_pembayaran_id" class="form-control select-tipe-bayar" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach(\App\Models\TipePembayaran::all() as $tipe)
                                        <option value="{{ $tipe->id }}" data-nama="{{ strtolower($tipe->nama) }}">
                                            {{ $tipe->nama }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- QR Code (muncul hanya jika pilih QRIS) --}}
                                <div class="qr-section mt-3" style="display:none;">
                                    <div class="text-center p-3 border rounded bg-light">
                                        <p class="font-weight-bold text-success mb-2">
                                            <i class="fas fa-qrcode mr-1"></i> Scan QR untuk Pembayaran
                                        </p>
                                        <img src="{{ asset('img/qr-kantin.png') }}" 
                                            alt="QR QRIS Kantin" 
                                            style="width: 200px; height: 200px; object-fit: contain;">
                                        <p class="text-muted small mt-2 mb-0">Scan menggunakan aplikasi mobile banking / e-wallet</p>
                                        <p class="text-danger small">Setelah pembayaran, upload bukti screenshot di bawah</p>
                                    </div>
                                </div>

                                {{-- Note Tunai --}}
                                <div class="tunai-section mt-3" style="display:none;">
                                    <div class="alert alert-success border-left-success shadow-sm mb-0">
                                        <h6 class="font-weight-bold mb-1"><i class="fas fa-money-bill-wave mr-1"></i> Pembayaran Tunai</h6>
                                        <p class="small mb-0">
                                            Silakan temui admin sistem sewa ruko/kantin di <strong>kantor koperasi pegawai SMKN 1 Cirebon</strong> untuk melakukan pembayaran secara tunai.
                                        </p>
                                    </div>
                                </div>

                                {{-- Info Transfer --}}
                                <div class="transfer-section mt-3" style="display:none;">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="font-weight-bold mb-2 text-primary"><i class="fas fa-university mr-1"></i> Rekening Tujuan</h6>
                                        <div class="text-sm">
                                            <p class="mb-1"><strong>BANK MANDIRI</strong></p>
                                            <p class="mb-1 h5 font-weight-bold">123-45678-9000-1</p>
                                            <p class="mb-0 text-muted">a.n KOPERASI SMKN 1 CIREBON</p>
                                        </div>
                                        <p class="text-danger small mt-2 mb-0">Setelah transfer, mohon upload bukti transfer di bawah ini.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group bukti-section">
                                <label class="font-weight-bold">Bukti Pembayaran <span class="text-danger">*</span></label>
                                <input type="file" name="bukti_pembayaran" class="form-control-file input-bukti" required
                                    accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted">Format: JPG, PNG, PDF. Maks 2MB.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif

@push('scripts')
<script>
    $(document).on('change', '.select-tipe-bayar', function() {
        const selectedOption = $(this).find('option:selected');
        const namaTipe = selectedOption.data('nama') || '';
        const modalBody = $(this).closest('.modal-body');
        
        const qrSection = modalBody.find('.qr-section');
        const tunaiSection = modalBody.find('.tunai-section');
        const transferSection = modalBody.find('.transfer-section');
        const buktiSection = modalBody.find('.bukti-section');
        const inputBukti = modalBody.find('.input-bukti');

        // Reset semua
        qrSection.hide();
        tunaiSection.hide();
        transferSection.hide();
        
        if (namaTipe.includes('qris')) {
            qrSection.slideDown();
            buktiSection.show();
            inputBukti.prop('required', true);
        } else if (namaTipe.includes('tunai')) {
            tunaiSection.slideDown();
            buktiSection.hide();
            inputBukti.prop('required', false);
        } else if (namaTipe.includes('transfer')) {
            transferSection.slideDown();
            buktiSection.show();
            inputBukti.prop('required', true);
        } else {
            // Default behaviour jika tidak memilih atau tipe tak dikenal
            buktiSection.show();
            inputBukti.prop('required', true);
        }
    });

    // Reset saat modal ditutup
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('.select-tipe-bayar').val('');
        $(this).find('.qr-section, .tunai-section, .transfer-section').hide();
        $(this).find('.bukti-section').show();
        $(this).find('.input-bukti').prop('required', true);
    });

    // Custom file name update
    $('.form-control-file').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        // logic is simpler here since it's not custom-file-input
    });
</script>
@endpush

@endsection
