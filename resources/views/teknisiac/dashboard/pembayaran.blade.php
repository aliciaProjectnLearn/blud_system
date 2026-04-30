@extends('layouts.app')

@section('title', 'Pembayaran AC')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail & Konfirmasi Pembayaran Pesanan</h1>
        <a href="{{ route('teknisi.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        {{-- Data Pelanggan --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pelanggan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">Nama Pelanggan</td>
                            <td>: <strong>{{ $pekerjaan->nama_pelanggan ?? optional($pekerjaan->user)->nama_lengkap ?? optional($pekerjaan->user)->name ?? 'Guest' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Telepon/HP</td>
                            <td>: {{ $pekerjaan->no_hp ?? optional($pekerjaan->user)->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat Lengkap</td>
                            <td>: {{ $pekerjaan->alamat }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Info Pesanan --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Servis AC</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">Layanan</td>
                            <td>: <strong>{{ $pekerjaan->layanan->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Merek AC</td>
                            <td>: {{ $pekerjaan->merek_ac ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tgl Kunjungan</td>
                            <td>: {{ \Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Status Pesanan</td>
                            <td>: 
                                <span class="badge badge-success">{{ ucfirst($pekerjaan->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Pembayaran & Rincian --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Rincian & Tagihan Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="font-weight-bold">Rincian Biaya:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Item Deskripsi</th>
                                    <th class="text-right">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Biaya Layanan Utama ({{ $pekerjaan->layanan->nama ?? 'N/A' }})</td>
                                    <td class="text-right">Rp {{ number_format($pekerjaan->layanan->harga_jasa ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @foreach($pekerjaan->detailServis as $detail)
                                    @if($detail->item !== 'Tindakan Servis (Tanpa Sparepart)')
                                        <tr>
                                            <td>Tambahan: {{ $detail->item }} (x{{ $detail->quantity }})</td>
                                            <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th class="text-right">Total Tagihan</th>
                                    <th class="text-right h5 text-primary mb-0 font-weight-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <hr>

            <h6 class="font-weight-bold text-gray-700 mb-3">Konfirmasi Pembayaran Oleh Teknisi</h6>
            <form action="{{ route('teknisi.pembayaran.store', $pekerjaan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="total_tagihan" value="{{ $totalTagihan }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bayar" class="form-control bg-light" value="{{ now()->format('Y-m-d') }}" readonly>
                            <small class="text-muted">Tanggal konfirmasi bersifat aktual saat ini</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipe Pembayaran <span class="text-danger">*</span></label>
                            <select name="tipe_pembayaran_id" id="tipe_pembayaran_id" class="form-control" required onchange="toggleQRIS()">
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($metodePembayaran as $metode)
                                    <option value="{{ $metode->id }}" data-nama="{{ strtolower($metode->nama) }}">{{ $metode->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tampilan QRIS Placeholder under Metode Pembayaran -->
                <div id="qris_container" class="row mt-2 mb-4" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="border rounded p-4 shadow-sm bg-light">
                            <h5 class="font-weight-bold mb-3 text-primary">Scan QRIS Berikut:</h5>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" alt="QRIS Placeholder" class="img-fluid mb-3 shadow-sm rounded bg-white p-2" style="max-height: 200px;">
                            <p class="text-dark mb-0">Minta pelanggan menscan kode ini untuk melunasi tagihan sebesar <strong>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="row align-items-end mt-3">
                    <div class="col-md-8">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Upload Bukti Pembayaran <small class="text-muted">(Khusus Transfer Bank / QRIS)</small></label>
                            <div class="custom-file">
                                <input type="file" name="bukti" class="custom-file-input" id="buktiInput" accept="image/*">
                                <label class="custom-file-label" for="buktiInput" data-browse="Pilih">Pilih file tangkapan layar / nota</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary btn-block py-2 pb-2" onclick="return confirm('Konfirmasi pembayaran ini sebagai lunas?')">
                            <i class="fas fa-paper-plane mr-2"></i> Konfirmasi Lunas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleQRIS() {
        var select = document.getElementById('tipe_pembayaran_id');
        var selectedOption = select.options[select.selectedIndex];
        var metodeNama = selectedOption ? selectedOption.getAttribute('data-nama') : '';
        var qrisContainer = document.getElementById('qris_container');
        
        if (metodeNama && metodeNama.includes('qris')) {
            qrisContainer.style.display = 'flex';
        } else {
            qrisContainer.style.display = 'none';
        }
    }

    // Nama file custom input update
    $('.custom-file-input').on('change',function(){
        var fileName = $(this).val().split('\\').pop();
        if(fileName) {
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        } else {
            $(this).next('.custom-file-label').removeClass("selected").html('Pilih file');
        }
    });
</script>
@endpush
