@extends('layouts.app')

@section('title', 'Tagihan Sewa Kantin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tagihan Sewa Saya</h1>
        <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Tagihan & Termin</h6>
                </div>
                <div class="card-body">
                    @forelse($tagihan as $item)
                        <div class="card mb-3 border-left-{{ $item->status == 'lunas' ? 'success' : ($item->status == 'verifikasi' ? 'info' : 'warning') }}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Termin {{ $item->termin }} - {{ $item->sewaRuko->ruko->kode_unit ?? 'Unit' }}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}
                                        </div>
                                        <div class="mt-2 small text-muted">
                                            <i class="fas fa-calendar-alt mr-1"></i> Jatuh Tempo: {{ \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="col-auto text-right">
                                        @if($item->status == 'menunggu')
                                            <span class="badge badge-warning mb-2 px-3 py-1">Menunggu Pembayaran</span>
                                            <div>
                                                <button class="btn btn-sm btn-primary" onclick="bayarTermin({{ $item->id }}, {{ $item->jumlah_tagihan }}, {{ $item->termin }})">
                                                    <i class="fas fa-upload mr-1"></i> Bayar Sekarang
                                                </button>
                                            </div>
                                        @elseif($item->status == 'verifikasi')
                                            <span class="badge badge-info mb-2 px-3 py-1">Menunggu Verifikasi</span>
                                            <div class="small text-muted">Bukti sudah diunggah</div>
                                        @elseif($item->status == 'lunas')
                                            <span class="badge badge-success mb-2 px-3 py-1">Lunas</span>
                                            <div>
                                                <a href="{{ route('user.kantin.riwayat') }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-file-invoice mr-1"></i> Lihat Kwitansi
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-gray-200 mb-3"></i>
                            <h5 class="text-gray-500">Semua tagihan Anda telah lunas!</h5>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Metode Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="font-weight-bold small text-uppercase text-muted">Transfer Bank</label>
                        <div class="d-flex align-items-center mb-2">
                            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1200px-BNI_logo.svg.png" height="20" class="mr-3">
                            <div>
                                <div class="font-weight-bold">BNI: 1234567890</div>
                                <div class="small text-muted">A.N. BLUD SMKN 1 CIREBON</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="font-weight-bold small text-uppercase text-muted">QRIS Statis</label>
                        <div class="text-center p-3 border rounded">
                           <!-- Placeholder QRIS -->
                           <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=BLUDSMKNCIR" alt="QRIS" class="img-fluid mb-2">
                           <div class="small font-weight-bold">SCAN UNTUK BAYAR</div>
                           <div class="extra-small text-muted mt-1" style="font-size: 10px;">Dukung pembayaran via GoPay, OVO, Dana, LinkAja, dll.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow">
            <form action="" id="formBayar" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold">Unggah Bukti Pembayaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="pembayaranInfo" class="mb-4">
                        <div class="alert alert-info border-0">
                            Poyeksi Pembayaran <strong id="txtTermin"></strong>: <br>
                            <h3 class="font-weight-bold mb-0" id="txtNominal"></h3>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Metode <span class="text-danger">*</span></label>
                        <select name="tipe_pembayaran_id" class="form-control" required>
                            <option value="1">Transfer Bank</option>
                            <option value="3">QRIS</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Bukti Transfer (JPG/PNG/PDF) <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" name="bukti_pembayaran" class="custom-file-input" id="customFile" required accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="customFile">Pilih file...</label>
                        </div>
                        <small class="text-muted">Maksimal ukuran file 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Kirim Bukti Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bayarTermin(id, nominal, termin) {
        // Set action URL (needs route implementation)
        const url = `/user/kantin/pembayaran/${id}/confirm`;
        document.getElementById('formBayar').action = url;
        
        document.getElementById('txtTermin').innerText = 'Termin ' + termin;
        document.getElementById('txtNominal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(nominal);
        
        $('#modalBayar').modal('show');
    }

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
